import prisma from '@/lib/prisma';
import { User, Shield, CreditCard, Clock } from 'lucide-react';

export default async function AdminUsersPage() {
  const users = await prisma.user.findMany({
    include: { ads: true, subscriptions: true, transactions: true },
    orderBy: { createdAt: 'desc' }
  });

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold">User Management</h1>
        <p className="text-gray-500">Overview of registered users and their platform activity.</p>
      </div>

      <div className="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-gray-50 border-b">
            <tr>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase">User Details</th>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Role</th>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Activity</th>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Registered</th>
            </tr>
          </thead>
          <tbody className="divide-y">
            {users.map((u) => (
              <tr key={u.id} className="hover:bg-gray-50 transition-colors">
                <td className="px-6 py-6 flex items-center gap-4">
                  <div className="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black">
                    {u.name?.[0] || 'U'}
                  </div>
                  <div>
                    <p className="font-bold text-gray-900">{u.name}</p>
                    <p className="text-xs text-gray-500">{u.email}</p>
                  </div>
                </td>
                <td className="px-6 py-6">
                  <div className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${u.role === 'ADMIN' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600'}`}>
                    {u.role === 'ADMIN' && <Shield className="w-3 h-3" />}
                    {u.role}
                  </div>
                </td>
                <td className="px-6 py-6">
                  <div className="flex gap-4 text-xs font-bold text-gray-500">
                    <span className="flex items-center gap-1"><CreditCard className="w-3.5 h-3.5" /> {u.transactions.length}</span>
                    <span className="flex items-center gap-1"><Clock className="w-3.5 h-3.5" /> {u.ads.length} ads</span>
                  </div>
                </td>
                <td className="px-6 py-6 text-right text-xs font-medium text-gray-500">
                  {new Date(u.createdAt).toLocaleDateString('en-GB')}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
