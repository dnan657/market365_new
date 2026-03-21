import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import { getServerSession } from 'next-auth';
import { authOptions } from './auth-options';

export async function checkAdminAuth() {
  const session = await getServerSession(authOptions);
  const userRole = (session?.user as any)?.role;
  return userRole === 'SUPERADMIN' || userRole === 'MODERATOR';
}

export async function protectAdminRoute() {
  const isAdmin = await checkAdminAuth();
  if (!isAdmin) {
    redirect('/admin/login');
  }
}

export async function isAdminAction() {
  const isAdmin = await checkAdminAuth();
  if (!isAdmin) {
    throw new Error('Unauthorized: Admin access required.');
  }
}

export async function isSuperAdmin() {
  const session = await getServerSession(authOptions);
  return (session?.user as any)?.role === 'SUPERADMIN';
}
