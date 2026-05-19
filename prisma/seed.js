import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';
const prisma = new PrismaClient();

async function main() {
  const adminRole = await prisma.role.upsert({ where:{name:'admin'}, update:{}, create:{name:'admin', permissions:'*'} });
  const managerRole = await prisma.role.upsert({ where:{name:'manager'}, update:{}, create:{name:'manager', permissions:'dashboard,products,categories'} });
  await prisma.user.upsert({ where:{email:'admin@example.com'}, update:{}, create:{name:'Admin', email:'admin@example.com', password:await bcrypt.hash('Admin123!',10), roleId:adminRole.id} });
  await prisma.user.upsert({ where:{email:'manager@example.com'}, update:{}, create:{name:'Manager', email:'manager@example.com', password:await bcrypt.hash('Manager123!',10), roleId:managerRole.id} });
  const cat = await prisma.category.upsert({ where:{name:'General'}, update:{}, create:{name:'General'} });
  await prisma.product.upsert({ where:{sku:'SKU-001'}, update:{}, create:{name:'Sample Product', sku:'SKU-001', price:99.99, stock:50, categoryId:cat.id} });
}
main().finally(()=>prisma.$disconnect());
