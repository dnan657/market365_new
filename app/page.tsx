import { Car, Home, Laptop, Briefcase, Dog, HelpCircle } from 'lucide-react';
import { CategoryCard } from '@/components/CategoryCard';
import { AdCard } from '@/components/AdCard';
import prisma from '@/lib/prisma';
import Link from 'next/link';

const categories = [
  { name: 'Cars & Vehicles', slug: 'Cars & Vehicles', icon: <Car /> },
  { name: 'Property', slug: 'Property', icon: <Home /> },
  { name: 'Jobs', slug: 'Jobs', icon: <Briefcase /> },
  { name: 'Electronics', slug: 'Electronics', icon: <Laptop /> },
  { name: 'Pets', slug: 'Pets', icon: <Dog /> },
  { name: 'Services', slug: 'Services', icon: <HelpCircle /> },
];

export default async function HomePage() {
  const latestAds = await prisma.ad.findMany({
    where: { status: 'APPROVED' },
    take: 8,
    orderBy: [
      { isPromoted: 'desc' },
      { createdAt: 'desc' }
    ],
    include: { images: true }
  });

  return (
    <div className="space-y-12">
      {/* Hero Section */}
      <section className="bg-blue-600 rounded-[3rem] py-20 px-8 text-white text-center shadow-2xl relative overflow-hidden">
        <div className="relative z-10 max-w-4xl mx-auto space-y-8">
          <h1 className="text-6xl font-black mb-4 tracking-tighter leading-tight">The UK's Favourite Marketplace</h1>
          <p className="text-2xl opacity-90 mb-8 max-w-2xl mx-auto font-medium">
            Buying and selling made simple. From cars to tech, and everything in between.
          </p>
          <div className="flex flex-wrap justify-center gap-6">
            <Link href="/ads" className="bg-white text-blue-600 px-10 py-4 rounded-2xl font-black text-lg hover:bg-gray-100 transition-all hover:scale-105 active:scale-95 shadow-xl">
              Start Buying
            </Link>
            <Link href="/ads/create" className="bg-yellow-400 text-blue-900 px-10 py-4 rounded-2xl font-black text-lg hover:bg-yellow-500 transition-all hover:scale-105 active:scale-95 shadow-xl">
              Sell an Item
            </Link>
          </div>
        </div>
        <div className="absolute top-0 right-0 w-96 h-96 bg-white/10 blur-[100px] -mr-48 -mt-48 rounded-full"></div>
        <div className="absolute bottom-0 left-0 w-96 h-96 bg-blue-400/20 blur-[100px] -ml-48 -mb-48 rounded-full"></div>
      </section>

      {/* Categories Grid */}
      <section className="space-y-8">
        <div className="flex items-end justify-between">
          <div>
            <h2 className="text-3xl font-black text-gray-900 tracking-tight">Explore by Category</h2>
            <p className="text-gray-500 font-medium">Find exactly what you're looking for</p>
          </div>
          <Link href="/ads" className="text-sm font-black text-blue-600 hover:underline bg-blue-50 px-4 py-2 rounded-xl">See all</Link>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
          {categories.map((cat) => (
            <CategoryCard
              key={cat.slug}
              name={cat.name}
              slug={cat.slug}
              icon={cat.icon}
            />
          ))}
        </div>
      </section>

      {/* Latest Ads Section */}
      <section className="space-y-8">
        <div>
          <h2 className="text-3xl font-black text-gray-900 tracking-tight">Latest Ads across the UK</h2>
          <p className="text-gray-500 font-medium">Fresh listings from your local community</p>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          {latestAds.length > 0 ? (
            latestAds.map((ad) => (
              <AdCard
                key={ad.id}
                id={ad.id}
                title={ad.title}
                price={ad.price}
                location={ad.location}
                category={ad.category}
                createdAt={ad.createdAt.toISOString()}
                imageUrl={ad.images[0]?.url}
                isPromoted={ad.isPromoted}
              />
            ))
          ) : (
            <div className="col-span-full py-20 text-center text-gray-400 border-4 border-dashed rounded-[2rem] bg-gray-50/50">
              <p className="text-xl font-bold">No ads found yet. Be the first to post!</p>
              <Link href="/ads/create" className="text-blue-600 font-black hover:underline mt-2 inline-block">Create a listing</Link>
            </div>
          )}
        </div>
      </section>

      {/* UK Benefits Section */}
      <section className="grid md:grid-cols-3 gap-8 py-16 px-8 bg-white border rounded-[3rem] shadow-sm">
        <div className="text-center p-6 space-y-4">
          <div className="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto text-3xl">🇬🇧</div>
          <h3 className="font-black text-xl">Local to You</h3>
          <p className="text-sm text-gray-500 font-medium leading-relaxed">Browse thousands of items within your local UK community.</p>
        </div>
        <div className="text-center p-6 space-y-4">
          <div className="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mx-auto text-3xl">🛡️</div>
          <h3 className="font-black text-xl">AI Protected</h3>
          <p className="text-sm text-gray-500 font-medium leading-relaxed">Shop with confidence using our advanced AI-driven safety tools.</p>
        </div>
        <div className="text-center p-6 space-y-4">
          <div className="w-16 h-16 bg-yellow-50 text-yellow-600 rounded-2xl flex items-center justify-center mx-auto text-3xl">💰</div>
          <h3 className="font-black text-xl">Best Value</h3>
          <p className="text-sm text-gray-500 font-medium leading-relaxed">Save money by buying used or grab a bargain near you today.</p>
        </div>
      </section>
    </div>
  );
}
