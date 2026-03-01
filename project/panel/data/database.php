<?php
/**
 * Database singleton — SQLite via PDO.
 *
 * The .sqlite file lives in data/ which should be writable by the web server
 * but NOT publicly accessible (add deny rules in production).
 */

function panel_db(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dbPath = __DIR__ . '/panel.sqlite';
    $isNew  = !file_exists($dbPath);

    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Enable WAL mode for better concurrent read performance
    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA foreign_keys=ON');

    if ($isNew) {
        panel_db_migrate($pdo);
        panel_db_seed($pdo);
    }

    return $pdo;
}

/**
 * Create all tables.
 */
function panel_db_migrate(PDO $pdo): void
{
    $now = date('Y-m-d H:i:s');

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            name       TEXT    NOT NULL,
            email      TEXT    NOT NULL UNIQUE,
            password   TEXT    NOT NULL,
            created_at TEXT    NOT NULL DEFAULT '{$now}'
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            name       TEXT    NOT NULL,
            email      TEXT    NOT NULL UNIQUE,
            role       TEXT    NOT NULL DEFAULT 'Operator',
            status     TEXT    NOT NULL DEFAULT 'Active',
            created_at TEXT    NOT NULL DEFAULT '{$now}',
            updated_at TEXT    NOT NULL DEFAULT '{$now}'
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id         TEXT    PRIMARY KEY,
            name       TEXT    NOT NULL,
            provider   TEXT    NOT NULL,
            stock      INTEGER NOT NULL DEFAULT 0,
            price      INTEGER NOT NULL DEFAULT 0,
            created_at TEXT    NOT NULL DEFAULT '{$now}',
            updated_at TEXT    NOT NULL DEFAULT '{$now}'
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id         TEXT    PRIMARY KEY,
            customer   TEXT    NOT NULL,
            amount     INTEGER NOT NULL DEFAULT 0,
            status     TEXT    NOT NULL DEFAULT 'Pending',
            date       TEXT    NOT NULL,
            created_at TEXT    NOT NULL DEFAULT '{$now}'
        )
    ");

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS settings (
            key   TEXT PRIMARY KEY,
            value TEXT NOT NULL
        )
    ');

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS audit_log (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            admin_id   INTEGER,
            action     TEXT    NOT NULL,
            target     TEXT,
            detail     TEXT,
            ip         TEXT,
            created_at TEXT    NOT NULL DEFAULT '{$now}'
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS login_attempts (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            email      TEXT    NOT NULL,
            ip         TEXT    NOT NULL,
            success    INTEGER NOT NULL DEFAULT 0,
            created_at TEXT    NOT NULL DEFAULT '{$now}'
        )
    ");

    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip, created_at)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_audit_log_admin ON audit_log(admin_id, created_at)');
}

/**
 * Seed initial data from the old static arrays.
 */
function panel_db_seed(PDO $pdo): void
{
    // --- Admin account ---
    $pdo->prepare('INSERT INTO admins (name, email, password) VALUES (?, ?, ?)')
        ->execute(['MNK Operator', 'mnk@gmail.com', password_hash('aaa123', PASSWORD_DEFAULT)]);

    // --- Users ---
    $users = [
        ['Raka Saputra', 'raka@operator.id', 'Operator', 'Active'],
        ['Nina Maharani', 'nina@operator.id', 'Finance',  'Active'],
        ['Bagas Pratama', 'bagas@operator.id', 'Support',  'Inactive'],
        ['Yosef Wijaya',  'yosef@operator.id', 'Admin',    'Active'],
    ];
    $stmt = $pdo->prepare('INSERT INTO users (name, email, role, status) VALUES (?, ?, ?, ?)');
    foreach ($users as $u) {
        $stmt->execute($u);
    }

    // --- Products ---
    $products = [
        ['PRD-120', 'Slot Package A',   'Pragmatic Play', 120, 25000],
        ['PRD-121', 'Slot Package B',   'PG Soft',        95,  30000],
        ['PRD-122', 'Live Casino Pass', 'Evolution',       52,  50000],
    ];
    $stmt = $pdo->prepare('INSERT INTO products (id, name, provider, stock, price) VALUES (?, ?, ?, ?, ?)');
    foreach ($products as $p) {
        $stmt->execute($p);
    }

    // --- Orders ---
    $orders = [
        ['INV-9021', 'Andi', 175000, 'Paid',    '2026-02-19'],
        ['INV-9022', 'Maya', 90000,  'Pending',  '2026-02-20'],
        ['INV-9023', 'Rani', 60000,  'Failed',   '2026-02-20'],
        ['INV-9024', 'Dewa', 220000, 'Paid',     '2026-02-21'],
    ];
    $stmt = $pdo->prepare('INSERT INTO orders (id, customer, amount, status, date) VALUES (?, ?, ?, ?, ?)');
    foreach ($orders as $o) {
        $stmt->execute($o);
    }

    // --- Default settings ---
    $settings = [
        ['brand', 'Operator Hitam Emas'],
        ['notifications', '1'],
    ];
    $stmt = $pdo->prepare('INSERT INTO settings (key, value) VALUES (?, ?)');
    foreach ($settings as $s) {
        $stmt->execute($s);
    }
}

/**
 * Helper: get a single setting value.
 */
function panel_setting(string $key, string $default = ''): string
{
    $stmt = panel_db()->prepare('SELECT value FROM settings WHERE key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['value'] : $default;
}

/**
 * Helper: set a setting value (upsert).
 */
function panel_setting_set(string $key, string $value): void
{
    $stmt = panel_db()->prepare('INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value');
    $stmt->execute([$key, $value]);
}

/**
 * Helper: log an audit event.
 */
function panel_audit(string $action, ?string $target = null, ?string $detail = null): void
{
    $adminId = $_SESSION['operator_admin_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $stmt = panel_db()->prepare('INSERT INTO audit_log (admin_id, action, target, detail, ip) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$adminId, $action, $target, $detail, $ip]);
}

/**
 * Rate limiting: check if IP is locked out.
 * Returns remaining seconds if locked, 0 if OK.
 */
function panel_rate_check(string $ip, int $maxAttempts = 5, int $windowSeconds = 300): int
{
    $db = panel_db();
    $since = date('Y-m-d H:i:s', time() - $windowSeconds);

    $stmt = $db->prepare('SELECT COUNT(*) as cnt, MAX(created_at) as last_at FROM login_attempts WHERE ip = ? AND success = 0 AND created_at > ?');
    $stmt->execute([$ip, $since]);
    $row = $stmt->fetch();

    if ($row['cnt'] >= $maxAttempts) {
        $lastTime = strtotime($row['last_at']);
        $unlockAt = $lastTime + $windowSeconds;
        $remaining = $unlockAt - time();
        return max(0, $remaining);
    }

    return 0;
}

/**
 * Record a login attempt.
 */
function panel_rate_record(string $email, string $ip, bool $success): void
{
    $stmt = panel_db()->prepare('INSERT INTO login_attempts (email, ip, success) VALUES (?, ?, ?)');
    $stmt->execute([$email, $ip, $success ? 1 : 0]);

    // On success, clear failed attempts for this IP
    if ($success) {
        $stmt = panel_db()->prepare('DELETE FROM login_attempts WHERE ip = ? AND success = 0');
        $stmt->execute([$ip]);
    }
}
