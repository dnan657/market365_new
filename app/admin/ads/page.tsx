import prisma from '@/lib/prisma';
import { Check, X, Eye, Trash2 } from 'lucide-react';
import { revalidatePath } from 'next/cache';
import { isAdminAction } from '@/lib/auth';

export default async function AdminAdsPage() {
  const pendingAds = await prisma.ad.findMany({
    where: { status: 'PENDING' },
    include: { author: true, images: true },
    orderBy: { createdAt: 'desc' }
  });

  async function approveAd(id: number) {
    'use server';
    await isAdminAction();
    await prisma.ad.update({
      where: { id },
      data: { status: 'APPROVED' }
    });
    revalidatePath('/admin/ads');
  }

  async function rejectAd(id: number) {
    'use server';
    await isAdminAction();
    await prisma.ad.update({
      where: { id },
      data: { status: 'REJECTED' }
    });
    revalidatePath('/admin/ads');
  }

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold">Ad Moderation</h1>
        <p className="text-gray-500">Review and approve new ad submissions from UK users.</p>
      </div>

      <div className="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <table className="w-full text-left">
          <thead className="bg-gray-50 border-b">
            <tr>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Ad Details</th>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Seller</th>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Price</th>
              <th className="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y">
            {pendingAds.length > 0 ? (
              pendingAds.map((ad) => (
                <tr key={ad.id} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-6">
                    <div className="flex items-center gap-4">
                      <div className="w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-400 overflow-hidden">
                        {ad.images[0] ? (
                          <img src={ad.images[0].url} alt={ad.title} className="w-full h-full object-cover" />
                        ) : (
                          'No Img'
                        )}
                      </div>
                      <div>
                        <p className="font-bold text-gray-900">{ad.title}</p>
                        <p className="text-xs text-gray-500 line-clamp-1">{ad.category} • {ad.location}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-6">
                    <p className="text-sm font-medium">{ad.author.name}</p>
                    <p className="text-xs text-gray-500">{ad.author.email}</p>
                  </td>
                  <td className="px-6 py-6">
                    <p className="text-sm font-bold text-blue-700">£{ad.price.toLocaleString()}</p>
                  </td>
                  <td className="px-6 py-6 text-right">
                    <div className="flex items-center justify-end gap-2">
                      <form action={approveAd.bind(null, ad.id)}>
                        <button className="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors" title="Approve">
                          <Check className="w-5 h-5" />
                        </button>
                      </form>
                      <form action={rejectAd.bind(null, ad.id)}>
                        <button className="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Reject">
                          <X className="w-5 h-5" />
                        </button>
                      </form>
                      <button className="p-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors" title="View Detail">
                        <Eye className="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan={4} className="px-6 py-20 text-center text-gray-500">
                  <p className="text-lg">No pending ads to moderate.</p>
                  <p className="text-sm">New submissions will appear here automatically.</p>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
