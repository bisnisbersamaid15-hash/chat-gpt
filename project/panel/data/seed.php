<?php
return [
  'stats' => [
    'revenue_today' => 12450000,
    'active_users' => 982,
    'pending_orders' => 27,
    'tickets' => 9,
  ],
  'users' => [
    ['id' => 1, 'name' => 'Raka Saputra', 'email' => 'raka@operator.id', 'role' => 'Operator', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Nina Maharani', 'email' => 'nina@operator.id', 'role' => 'Finance', 'status' => 'Active'],
    ['id' => 3, 'name' => 'Bagas Pratama', 'email' => 'bagas@operator.id', 'role' => 'Support', 'status' => 'Inactive'],
    ['id' => 4, 'name' => 'Yosef Wijaya', 'email' => 'yosef@operator.id', 'role' => 'Admin', 'status' => 'Active'],
  ],
  'products' => [
    ['id' => 'PRD-120', 'name' => 'Slot Package A', 'provider' => 'Pragmatic Play', 'stock' => 120, 'price' => 25000],
    ['id' => 'PRD-121', 'name' => 'Slot Package B', 'provider' => 'PG Soft', 'stock' => 95, 'price' => 30000],
    ['id' => 'PRD-122', 'name' => 'Live Casino Pass', 'provider' => 'Evolution', 'stock' => 52, 'price' => 50000],
  ],
  'orders' => [
    ['id' => 'INV-9021', 'customer' => 'Andi', 'amount' => 175000, 'status' => 'Paid', 'date' => '2026-02-19'],
    ['id' => 'INV-9022', 'customer' => 'Maya', 'amount' => 90000, 'status' => 'Pending', 'date' => '2026-02-20'],
    ['id' => 'INV-9023', 'customer' => 'Rani', 'amount' => 60000, 'status' => 'Failed', 'date' => '2026-02-20'],
    ['id' => 'INV-9024', 'customer' => 'Dewa', 'amount' => 220000, 'status' => 'Paid', 'date' => '2026-02-21'],
  ],
];
