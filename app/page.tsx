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
    take: 8,
    orderBy: { createdAt: 'desc' },
  });

  return (
    <div className="space-y-12">
      {/* Hero Section */}
      <section className="bg-blue-600 rounded-2xl py-12 px-8 text-white text-center shadow-xl">
        <h1 className="text-4xl font-extrabold mb-4">The UK's Favourite Marketplace</h1>
        <p className="text-xl opacity-90 mb-8 max-w-2xl mx-auto">
          Buying and selling made simple. From cars to tech, and everything in between.
        </p>
        <div className="flex flex-wrap justify-center gap-4">
          <Link href="/ads" className="bg-white text-blue-600 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition-colors shadow-lg">
            Start Buying
          </Link>
          <Link href="/ads/create" className="bg-yellow-400 text-blue-900 px-8 py-3 rounded-full font-bold hover:bg-yellow-500 transition-colors shadow-lg">
            Sell an Item
          </Link>
        </div>
      </section>

      {/* Categories Grid */}
      <section>
        <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center justify-between">
          <span>Explore by Category</span>
          <Link href="/ads" className="text-sm font-semibold text-blue-600 hover:underline">See all</Link>
        </h2>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
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
      <section>
        <h2 className="text-2xl font-bold text-gray-900 mb-6">Latest Ads across the UK</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
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
                imageUrl={ad.imageUrl}
              />
            ))
          ) : (
            <div className="col-span-full py-20 text-center text-gray-500 border-2 border-dashed rounded-xl">
              <p className="text-lg">No ads found yet. Be the first to post!</p>
            </div>
          )}
        </div>
      </section>

      {/* UK Benefits Section */}
      <section className="grid md:grid-cols-3 gap-8 py-8 border-t border-b border-gray-100">
        <div className="text-center p-4">
          <div className="text-3xl mb-2">🇬🇧</div>
          <h3 className="font-bold mb-1">Local to You</h3>
          <p className="text-sm text-gray-600">Browse thousands of items within your local community.</p>
        </div>
        <div className="text-center p-4">
          <div className="text-3xl mb-2">🛡️</div>
          <h3 className="font-bold mb-1">Trusted Sellers</h3>
          <p className="text-sm text-gray-600">Shop with confidence using our safety guidelines.</p>
        </div>
        <div className="text-center p-4">
          <div className="text-3xl mb-2">💰</div>
          <h3 className="font-bold mb-1">Great Value</h3>
          <p className="text-sm text-gray-600">Save money by buying used or grab a bargain near you.</p>
        </div>
      </section>
    </div>
  );
}
