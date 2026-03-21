'use client';

import { useState } from 'react';
import { CreditCard, Shield, Zap, Info, Loader2 } from 'lucide-react';

interface PromotePageClientProps {
  adId: number;
}

export default function PromotePageClient({ adId }: PromotePageClientProps) {
  const [isLoading, setIsLoading] = useState(false);

  const handlePayment = async () => {
    setIsLoading(true);
    try {
      const res = await fetch('/api/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ adId }),
      });
      const data = await res.json();
      if (data.url) {
        window.location.href = data.url;
      }
    } catch (err) {
      console.error('Checkout failed', err);
    } finally {
      setIsLoading(false);
    }
  };

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

              <button
                onClick={handlePayment}
                disabled={isLoading}
                className="w-full bg-blue-600 text-white py-5 rounded-3xl font-black text-xl flex items-center justify-center gap-3 hover:bg-blue-700 transition-all hover:scale-[1.02] active:scale-[0.98] shadow-2xl shadow-blue-200 disabled:bg-gray-400"
              >
                {isLoading ? <Loader2 className="w-6 h-6 animate-spin" /> : <CreditCard className="w-6 h-6" />}
                {isLoading ? 'Redirecting to Stripe...' : 'Pay £9.99 with Stripe'}
              </button>

              <div className="flex items-center justify-center gap-2 mt-6 text-gray-400">
                <Shield className="w-4 h-4" />
                <span className="text-[10px] font-black uppercase tracking-widest">Secure 256-bit SSL Encryption</span>
              </div>
            </div>
          </div>
        </div>

        <div className="space-y-6 text-gray-600">
           <div className="bg-gray-900 p-8 rounded-[2rem] text-white space-y-8 shadow-2xl">
            <h3 className="text-2xl font-black leading-tight border-b border-white/10 pb-4">What you get</h3>
            <ul className="space-y-6">
              <li className="flex items-start gap-4">
                <div className="w-8 h-8 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 shrink-0 text-sm">✓</div>
                <p className="text-sm font-bold opacity-80 leading-relaxed">Featured placement at the very top of search results for 7 days.</p>
              </li>
              <li className="flex items-start gap-4">
                <div className="w-8 h-8 bg-purple-500/20 rounded-xl flex items-center justify-center text-purple-400 shrink-0 text-sm">✓</div>
                <p className="text-sm font-bold opacity-80 leading-relaxed">Exclusive "Featured" badge on your ad card to catch buyer eyes.</p>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
