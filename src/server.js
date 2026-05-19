import express from 'express';
import session from 'express-session';
import cookieParser from 'cookie-parser';
import helmet from 'helmet';
import methodOverride from 'method-override';
import path from 'path';
import { fileURLToPath } from 'url';
import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();
const app = express();
const __dirname = path.dirname(fileURLToPath(import.meta.url));
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use('/public', express.static(path.join(__dirname, 'public')));
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser()); app.use(helmet()); app.use(methodOverride('_method'));
app.use(session({ secret: process.env.SESSION_SECRET || 'dev', resave:false, saveUninitialized:false }));

app.use(async (req,res,next)=>{ res.locals.user=null; if(req.session.userId){res.locals.user=await prisma.user.findUnique({where:{id:req.session.userId}, include:{role:true}});} next();});
const auth=(req,res,next)=> req.session.userId?next():res.redirect('/login');

app.get('/login',(req,res)=>res.send(`<form method='post'><input name='email'/><input name='password' type='password'/><button>Login</button></form>`));
app.post('/login', async (req,res)=>{ const u=await prisma.user.findUnique({where:{email:req.body.email}, include:{role:true}}); if(!u||!(await bcrypt.compare(req.body.password,u.password))) return res.status(401).send('Invalid'); req.session.userId=u.id; res.redirect('/dashboard'); });
app.post('/logout',(req,res)=>req.session.destroy(()=>res.redirect('/login')));
app.get('/',(req,res)=>res.redirect('/dashboard'));
app.get('/dashboard', auth, async (req,res)=>{
  const [users,products,categories,logs]=await Promise.all([prisma.user.count(),prisma.product.count(),prisma.category.count(),prisma.activityLog.findMany({take:10,orderBy:{createdAt:'desc'},include:{user:true}})]);
  res.json({message:'Dashboard ready',stats:{users,products,categories},logs});
});
app.get('/api/products', auth, async (req,res)=>{const page=Number(req.query.page||1),take=10,q=req.query.q||''; const where=q?{name:{contains:q}}:{}; const [data,total]=await Promise.all([prisma.product.findMany({where,take,skip:(page-1)*take,include:{category:true}}),prisma.product.count({where})]); res.json({data,total,page});});
app.post('/api/products', auth, async (req,res)=>{const b=req.body; const p=await prisma.product.create({data:{name:b.name,sku:b.sku,price:Number(b.price),stock:Number(b.stock),categoryId:Number(b.categoryId)}}); await prisma.activityLog.create({data:{userId:req.session.userId,action:'create',entity:'product',entityId:p.id}}); res.json(p);});

app.listen(process.env.PORT||3000,()=>console.log('running'));
