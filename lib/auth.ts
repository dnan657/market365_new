import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';

export async function checkAdminAuth() {
  const cookieStore = await cookies();
  const isAdmin = cookieStore.get('is_admin')?.value === 'true';
  return isAdmin;
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
