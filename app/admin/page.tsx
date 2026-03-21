import prisma from '@/lib/prisma';
import { DollarSign, FileText, Users, MessageCircle } from 'lucide-react';

export default async function AdminDashboardPage() {
  const adCount = await prisma.ad.count();
  const userCount = await prisma.user.count();
  const ticketCount = await prisma.ticket.count();
  const transactionTotal = await prisma.transaction.aggregate({
    _sum: { amount: true }
  });

  const stats = [
    { name: 'Total Revenue', value: `£${transactionTotal._sum.amount || 0}`, icon: <DollarSign className="w-6 h-6 text-green-600" />, change: '+12%', color: 'bg-green-100' },
    { name: 'Active Ads', value: adCount, icon: <FileText className="w-6 h-6 text-blue-600" />, change: '+5%', color: 'bg-blue-100' },
    { name: 'Total Users', value: userCount, icon: <Users className="w-6 h-6 text-purple-600" />, change: '+8%', color: 'bg-purple-100' },
    { name: 'Pending Tickets', value: ticketCount, icon: <MessageCircle className="w-6 h-6 text-orange-600" />, change: '-2%', color: 'bg-orange-100' },
  ];

  const recentAds = await prisma.ad.findMany({
    take: 5,
    orderBy: { createdAt: 'desc' },
    include: { author: true }
  });

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold">Platform Overview</h1>
        <p className="text-gray-500">Welcome to the UK Classifieds Admin Dashboard.</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <div key={stat.name} className="bg-white p-6 rounded-2xl border shadow-sm flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-500">{stat.name}</p>
              <p className="text-2xl font-bold mt-1">{stat.value}</p>
              <p className="text-xs font-bold text-green-600 mt-1">{stat.change} vs last month</p>
            </div>
            <div className={`p-3 rounded-xl ${stat.color}`}>
              {stat.icon}
            </div>
          </div>
        ))}
      </div>

      <div className="grid lg:grid-cols-2 gap-8">
        <div className="bg-white p-6 rounded-2xl border shadow-sm">
          <h2 className="text-xl font-bold mb-6">Recent Ads</h2>
          <div className="space-y-4">
            {recentAds.map((ad) => (
              <div key={ad.id} className="flex items-center justify-between pb-4 border-b last:border-0 last:pb-0">
                <div>
                  <p className="font-bold text-sm">{ad.title}</p>
                  <p className="text-xs text-gray-500">{ad.author.name} • {ad.location}</p>
                </div>
                <div className={`px-2 py-1 rounded-full text-[10px] font-bold uppercase ${ad.status === 'APPROVED' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'}`}>
                  {ad.status}
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl border shadow-sm">
          <h2 className="text-xl font-bold mb-6">Recent Admin Actions</h2>
          <div className="space-y-4">
            {/* Mocked Admin Log */}
            <div className="flex items-start gap-4">
              <div className="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">AM</div>
              <div>
                <p className="text-sm font-bold">Ad Approved</p>
                <p className="text-xs text-gray-500">"Ford Fiesta 2018" by UK Admin 5 mins ago</p>
              </div>
            </div>
            <div className="flex items-start gap-4">
              <div className="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-bold">TC</div>
              <div>
                <p className="text-sm font-bold">Ticket Resolved</p>
                <p className="text-xs text-gray-500">"Issue with ad upload" by UK Admin 2 hours ago</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
