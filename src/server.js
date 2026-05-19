import express from 'express';
import session from 'express-session';
import cookieParser from 'cookie-parser';
import helmet from 'helmet';
import methodOverride from 'method-override';
import path from 'path';
import { fileURLToPath } from 'url';
import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';
import { z } from 'zod';
import rateLimit from 'express-rate-limit';

const prisma = new PrismaClient();
const app = express();
const __dirname = path.dirname(fileURLToPath(import.meta.url));
const isProd = process.env.NODE_ENV === 'production';

if (process.env.TRUST_PROXY === 'true') {
  app.set('trust proxy', 1);
}

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use('/public', express.static(path.join(__dirname, 'public')));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());
app.use(helmet());
app.use(methodOverride('_method'));
app.use(
  session({
    secret: process.env.SESSION_SECRET || 'dev',
    resave: false,
    saveUninitialized: false,
    cookie: {
      httpOnly: true,
      secure: isProd,
      sameSite: 'lax',
      maxAge: 1000 * 60 * 60 * 8
    }
  })
);

const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 10,
  standardHeaders: true,
  legacyHeaders: false,
  message: { error: 'Too many login attempts. Please retry later.' }
});

const loginSchema = z.object({
  email: z.string().email(),
  password: z.string().min(8)
});

const productCreateSchema = z.object({
  name: z.string().min(2),
  sku: z.string().min(3),
  price: z.coerce.number().nonnegative(),
  stock: z.coerce.number().int().nonnegative(),
  categoryId: z.coerce.number().int().positive()
});

app.use(async (req, res, next) => {
  res.locals.user = null;
  if (req.session.userId) {
    res.locals.user = await prisma.user.findUnique({ where: { id: req.session.userId }, include: { role: true } });
  }
  next();
});

const auth = (req, res, next) => {
  if (!req.session.userId) return res.status(401).json({ error: 'Unauthorized' });
  return next();
};

app.get('/healthz', (req, res) => res.json({ status: 'ok', time: new Date().toISOString() }));
app.get('/readyz', async (req, res, next) => {
  try {
    await prisma.$queryRaw`SELECT 1`;
    res.json({ status: 'ready' });
  } catch (error) {
    next(error);
  }
});

app.get('/login', (req, res) =>
  res.send("<form method='post'><input name='email'/><input name='password' type='password'/><button>Login</button></form>")
);

app.post('/login', loginLimiter, async (req, res, next) => {
  try {
    const payload = loginSchema.parse(req.body);
    const user = await prisma.user.findUnique({ where: { email: payload.email }, include: { role: true } });
    if (!user || !(await bcrypt.compare(payload.password, user.password))) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }
    req.session.userId = user.id;
    return res.json({ message: 'Login success' });
  } catch (error) {
    next(error);
  }
});

app.post('/logout', (req, res) => req.session.destroy(() => res.json({ message: 'Logout success' })));
app.get('/', (req, res) => res.redirect('/dashboard'));

app.get('/dashboard', auth, async (req, res, next) => {
  try {
    const [users, products, categories, logs] = await Promise.all([
      prisma.user.count(),
      prisma.product.count(),
      prisma.category.count(),
      prisma.activityLog.findMany({ take: 10, orderBy: { createdAt: 'desc' }, include: { user: true } })
    ]);

    res.json({ message: 'Dashboard ready', stats: { users, products, categories }, logs });
  } catch (error) {
    next(error);
  }
});

app.get('/api/products', auth, async (req, res, next) => {
  try {
    const page = Math.max(Number(req.query.page || 1), 1);
    const take = Math.min(Math.max(Number(req.query.limit || 10), 1), 100);
    const q = (req.query.q || '').toString();
    const where = q ? { name: { contains: q } } : {};

    const [data, total] = await Promise.all([
      prisma.product.findMany({ where, take, skip: (page - 1) * take, include: { category: true } }),
      prisma.product.count({ where })
    ]);

    res.json({ data, total, page, limit: take });
  } catch (error) {
    next(error);
  }
});

app.post('/api/products', auth, async (req, res, next) => {
  try {
    const body = productCreateSchema.parse(req.body);
    const created = await prisma.product.create({ data: body });
    await prisma.activityLog.create({
      data: { userId: req.session.userId, action: 'create', entity: 'product', entityId: created.id }
    });
    res.status(201).json(created);
  } catch (error) {
    next(error);
  }
});

app.use((error, req, res, next) => {
  if (error instanceof z.ZodError) {
    return res.status(400).json({ error: 'Validation failed', details: error.flatten() });
  }

  console.error(error);
  return res.status(500).json({ error: 'Internal server error' });
});

app.listen(process.env.PORT || 3000, () => console.log('running'));
