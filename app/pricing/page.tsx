import { Check, Star, Zap, Shield, Rocket } from 'lucide-react';
import Link from 'next/link';

export default function PricingPage() {
  const plans = [
    {
      name: 'Standard User',
      price: 'Free',
      description: 'Perfect for selling a few items around the house.',
      features: ['Up to 5 active ads', 'Basic statistics', 'Standard visibility', '7-day ad duration'],
      buttonText: 'Get Started',
      color: 'bg-white',
      border: 'border-gray-200'
    },
    {
      name: 'Premium Seller',
      price: '£19.99',
      period: '/month',
      description: 'For power sellers and small businesses in the UK.',
      features: ['Unlimited active ads', 'Advanced analytics', '24-hour priority support', 'Featured listing badges', 'Ads stay for 30 days', 'Promote 3 ads / month'],
      buttonText: 'Try Premium',
      color: 'bg-blue-600',
      textColor: 'text-white',
      popular: true,
      border: 'border-blue-600'
    },
    {
      name: 'Business Elite',
      price: '£49.99',
      period: '/month',
      description: 'The ultimate tool for large UK retailers and car dealers.',
      features: ['Dedicated account manager', 'API access for bulk uploads', 'Auto-renew listings', 'Custom business profile', 'Premium placement in search', 'Promote 10 ads / month'],
      buttonText: 'Contact Sales',
      color: 'bg-white',
      border: 'border-gray-200'
    }
  ];

  return (
    <div className="space-y-16 py-12">
      <div className="text-center max-w-3xl mx-auto space-y-4">
        <h1 className="text-5xl font-black text-gray-900 tracking-tight leading-tight">Professional Selling Tools for the UK Marketplace</h1>
        <p className="text-xl text-gray-500 leading-relaxed">Reach more buyers and sell faster with our premium features and business packages.</p>
      </div>

      <div className="grid lg:grid-cols-3 gap-8">
        {plans.map((plan) => (
          <div key={plan.name} className={`relative p-10 rounded-3xl border shadow-xl flex flex-col gap-8 transition-all hover:scale-[1.03] ${plan.color} ${plan.textColor || 'text-gray-900'} ${plan.border} ${plan.popular ? 'ring-4 ring-blue-100' : ''}`}>
            {plan.popular && (
              <div className="absolute top-0 right-10 -translate-y-1/2 bg-yellow-400 text-blue-900 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest shadow-md">
                Most Popular
              </div>
            )}
            <div className="space-y-4">
              <h2 className="text-2xl font-black">{plan.name}</h2>
              <div className="flex items-baseline gap-1">
                <span className="text-4xl font-black">{plan.price}</span>
                {plan.period && <span className="text-sm font-medium opacity-80">{plan.period}</span>}
              </div>
              <p className="text-sm opacity-80 font-medium leading-relaxed">{plan.description}</p>
            </div>

            <ul className="space-y-4 flex-1">
              {plan.features.map((feature) => (
                <li key={feature} className="flex items-start gap-3 text-sm font-bold">
                  <Check className={`w-5 h-5 shrink-0 ${plan.popular ? 'text-white' : 'text-blue-600'}`} />
                  {feature}
                </li>
              ))}
            </ul>

            <Link href="/signup" className={`w-full py-4 rounded-2xl font-black text-lg text-center transition-all ${plan.popular ? 'bg-white text-blue-600 hover:bg-gray-100' : 'bg-blue-600 text-white hover:bg-blue-700'}`}>
              {plan.buttonText}
            </Link>
          </div>
        ))}
      </div>

      <section className="bg-gray-900 rounded-[3rem] p-12 text-white overflow-hidden relative">
        <div className="max-w-4xl mx-auto text-center space-y-8 relative z-10">
          <h2 className="text-4xl font-black">Why choose UK Classifieds?</h2>
          <div className="grid md:grid-cols-4 gap-8">
            <div className="space-y-3">
              <div className="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mx-auto text-blue-400">
                <Star className="w-6 h-6" />
              </div>
              <h3 className="font-bold">Trust</h3>
              <p className="text-xs text-gray-400">Verified UK-only userbase and moderation.</p>
            </div>
            <div className="space-y-3">
              <div className="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mx-auto text-purple-400">
                <Zap className="w-6 h-6" />
              </div>
              <h3 className="font-bold">Speed</h3>
              <p className="text-xs text-gray-400">Items sell within 48h on average.</p>
            </div>
            <div className="space-y-3">
              <div className="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mx-auto text-green-400">
                <Shield className="w-6 h-6" />
              </div>
              <h3 className="font-bold">Safe</h3>
              <p className="text-xs text-gray-400">Advanced AI protection for all users.</p>
            </div>
            <div className="space-y-3">
              <div className="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mx-auto text-yellow-400">
                <Rocket className="w-6 h-6" />
              </div>
              <h3 className="font-bold">Reach</h3>
              <p className="text-xs text-gray-400">Millions of monthly UK buyers.</p>
            </div>
          </div>
        </div>
        <div className="absolute top-0 right-0 w-96 h-96 bg-blue-600/20 blur-[100px] -mr-48 -mt-48 rounded-full"></div>
        <div className="absolute bottom-0 left-0 w-96 h-96 bg-purple-600/20 blur-[100px] -ml-48 -mb-48 rounded-full"></div>
      </section>
    </div>
  );
}
