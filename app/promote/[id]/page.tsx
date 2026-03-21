import prisma from '@/lib/prisma';
import { notFound, redirect } from 'next/navigation';
import { CreditCard, Shield, Zap, Info, CheckCircle2 } from 'lucide-react';

export default async function PromotePage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const adId = parseInt(id);

  if (isNaN(adId)) notFound();

  const ad = await prisma.ad.findUnique({
    where: { id: adId },
  });

  if (!ad) notFound();

  async function handlePayment() {
    'use server';
    // Re-verify existence inside server action
    const currentAd = await prisma.ad.findUnique({ where: { id: adId } });
    if (!currentAd) return;

    // Simulate payment processing and promotion activation
    await prisma.ad.update({
      where: { id: adId },
      data: { isPromoted: true }
    });

    // Create transaction record
    await prisma.transaction.create({
      data: {
        amount: 9.99,
        type: 'PROMOTION',
        userId: currentAd.authorId,
        status: 'COMPLETED'
      }
    });

    redirect(`/ads/${adId}?promoted=true`);
  }

  return (
    <div className="max-w-4xl mx-auto space-y-12 py-10">
      <div className="text-center space-y-4">
        <h1 className="text-5xl font-black text-gray-900 leading-tight tracking-tight">Promote Your Listing</h1>
        <p className="text-xl text-gray-500 leading-relaxed max-w-2xl mx-auto">Get up to 10x more views by featuring your ad at the top of UK search results.</p>
      </div>

      <div className="grid md:grid-cols-3 gap-8">
        <div className="md:col-span-2 space-y-8">
          <div className="bg-white p-10 rounded-[2.5rem] border shadow-xl shadow-gray-100 space-y-8">
            <h2 className="text-2xl font-black text-gray-900 border-b pb-4">Secure Checkout</h2>

            <div className="space-y-6">
              <div className="flex items-center justify-between bg-blue-50 p-6 rounded-2xl border border-blue-100">
                <div className="flex items-center gap-4">
                  <div className="p-3 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-200">
                    <Zap className="w-6 h-6" />
                  </div>
                  <div>
                    <p className="font-black text-lg text-blue-900">7-Day Homepage Feature</p>
                    <p className="text-xs text-blue-700 font-bold uppercase tracking-widest">Promotion Package</p>
                  </div>
                </div>
                <p className="text-2xl font-black text-blue-900">£9.99</p>
              </div>

              <div className="space-y-4">
                <label className="text-xs font-black text-gray-500 uppercase tracking-widest">Select Payment Method</label>
                <div className="grid grid-cols-2 gap-4">
                  <button className="flex items-center justify-center gap-3 p-4 border-2 border-blue-600 rounded-2xl bg-blue-50/50 transition-all ring-4 ring-blue-50">
                    <CreditCard className="w-5 h-5 text-blue-600" />
                    <span className="font-black text-sm text-blue-900 tracking-tight">Credit/Debit Card</span>
                  </button>
                  <button className="flex items-center justify-center gap-3 p-4 border-2 border-gray-100 rounded-2xl hover:border-blue-200 transition-all text-gray-400">
                    <span className="font-black text-sm tracking-tight italic text-blue-800">PayPal</span>
                  </button>
                </div>
              </div>

              <form action={handlePayment} className="pt-6">
                <button type="submit" className="w-full bg-blue-600 text-white py-5 rounded-3xl font-black text-xl flex items-center justify-center gap-3 hover:bg-blue-700 transition-all hover:scale-[1.02] active:scale-[0.98] shadow-2xl shadow-blue-200">
                   Pay £9.99 Now
                </button>
                <div className="flex items-center justify-center gap-2 mt-6 text-gray-400">
                  <Shield className="w-4 h-4" />
                  <span className="text-[10px] font-black uppercase tracking-widest">Secure 256-bit SSL Encryption</span>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-gray-900 p-8 rounded-[2rem] text-white space-y-8 shadow-2xl">
            <h3 className="text-2xl font-black leading-tight border-b border-white/10 pb-4">What you get</h3>
            <ul className="space-y-6">
              <li className="flex items-start gap-4">
                <div className="w-8 h-8 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 shrink-0">
                  <CheckCircle2 className="w-5 h-5" />
                </div>
                <p className="text-sm font-bold opacity-80 leading-relaxed">Featured placement at the very top of search results for 7 days.</p>
              </li>
              <li className="flex items-start gap-4">
                <div className="w-8 h-8 bg-purple-500/20 rounded-xl flex items-center justify-center text-purple-400 shrink-0">
                  <CheckCircle2 className="w-5 h-5" />
                </div>
                <p className="text-sm font-bold opacity-80 leading-relaxed">Exclusive "Featured" badge on your ad card to catch buyer eyes.</p>
              </li>
              <li className="flex items-start gap-4">
                <div className="w-8 h-8 bg-green-500/20 rounded-xl flex items-center justify-center text-green-400 shrink-0">
                  <CheckCircle2 className="w-5 h-5" />
                </div>
                <p className="text-sm font-bold opacity-80 leading-relaxed">Detailed performance reports showing views and clicks.</p>
              </li>
            </ul>
          </div>

          <div className="bg-yellow-50 p-8 rounded-[2rem] border border-yellow-200 text-yellow-800">
            <div className="flex items-center gap-2 mb-4">
              <Info className="w-5 h-5" />
              <h4 className="font-black text-lg">Good to know</h4>
            </div>
            <p className="text-sm font-bold opacity-80 leading-relaxed">Promoted ads receive an average of 400% more clicks than standard listings in the UK.</p>
          </div>
        </div>
      </div>
    </div>
  );
}
