import Link from 'next/link';
import { LayoutDashboard, FileText, Settings, Users, MessageSquare } from 'lucide-react';
import { protectAdminRoute } from '@/lib/auth';

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  // Simple check to see if the user has the is_admin cookie.
  // This happens server-side, so the user never sees any of the children if unauthorized.
  await protectAdminRoute();

  return (
    <div className="flex min-h-screen bg-gray-50 -mx-4 -my-8">
      <aside className="w-64 bg-white border-r h-screen sticky top-0">
        <div className="p-6 border-b">
          <Link href="/admin" className="text-xl font-bold text-blue-600 flex items-center gap-2">
            <span className="bg-blue-600 text-white px-2 py-1 rounded">Admin</span>
            UK Portal
          </Link>
        </div>
        <nav className="p-4 space-y-2">
          <Link href="/admin" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700 font-medium">
            <LayoutDashboard className="w-5 h-5" />
            Dashboard
          </Link>
          <Link href="/admin/ads" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700 font-medium">
            <FileText className="w-5 h-5" />
            Moderate Ads
          </Link>
          <Link href="/admin/tickets" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700 font-medium">
            <MessageSquare className="w-5 h-5" />
            Support Tickets
          </Link>
          <Link href="/admin/users" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700 font-medium">
            <Users className="w-5 h-5" />
            Users
          </Link>
          <div className="pt-4 border-t mt-4">
            <Link href="/" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-500 font-medium text-sm">
              <Settings className="w-4 h-4" />
              Main Website
            </Link>
          </div>
        </nav>
      </aside>
      <main className="flex-1 p-8">
        {children}
      </main>
    </div>
  );
}
