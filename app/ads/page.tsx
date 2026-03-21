import prisma from '@/lib/prisma';
import { AdCard } from '@/components/AdCard';
import { Search, MapPin, SlidersHorizontal } from 'lucide-react';
import Link from 'next/link';

export default async function AdsPage({
  searchParams,
}: {
  searchParams: Promise<{ [key: string]: string | string[] | undefined }>;
}) {
  const params = await searchParams;
  const category = typeof params.category === 'string' ? params.category : undefined;
  const q = typeof params.q === 'string' ? params.q : undefined;
  const location = typeof params.location === 'string' ? params.location : undefined;
  const minPrice = typeof params.minPrice === 'string' ? parseFloat(params.minPrice) : undefined;
  const maxPrice = typeof params.maxPrice === 'string' ? parseFloat(params.maxPrice) : undefined;

  const ads = await prisma.ad.findMany({
    where: {
      AND: [
        category ? { category: { contains: category } } : {},
        q ? {
          OR: [
            { title: { contains: q } },
            { description: { contains: q } },
          ]
        } : {},
        location ? { location: { contains: location } } : {},
        minPrice ? { price: { gte: minPrice } } : {},
        maxPrice ? { price: { lte: maxPrice } } : {},
        { status: 'APPROVED' } // Only show approved ads to public
      ]
    },
    include: { images: true },
    orderBy: { createdAt: 'desc' },
  });

  const locations = ["London", "Manchester", "Birmingham", "Leeds", "Glasgow", "Bristol", "Edinburgh"];
  const categories = ["Cars & Vehicles", "Property", "Electronics", "Home & Garden", "Pets", "Jobs"];

  return (
    <div className="flex flex-col md:flex-row gap-8">
      {/* Sidebar Filters */}
      <aside className="w-full md:w-64 space-y-6">
        <div className="flex items-center gap-2 font-bold text-lg mb-4">
          <SlidersHorizontal className="w-5 h-5" />
          <h2>Filters</h2>
        </div>

        <div>
          <h3 className="font-semibold mb-2 text-sm uppercase text-gray-500">UK Region</h3>
          <div className="space-y-1">
            <Link href="/ads" className={`block text-sm hover:text-blue-600 ${!location ? 'font-bold text-blue-600' : 'text-gray-600'}`}>
              All UK
            </Link>
            {locations.map(loc => (
              <Link
                key={loc}
                href={`/ads?location=${loc}`}
                className={`block text-sm hover:text-blue-600 ${location?.toLowerCase() === loc.toLowerCase() ? 'font-bold text-blue-600' : 'text-gray-600'}`}
              >
                {loc}
              </Link>
            ))}
          </div>
        </div>

        <div>
          <h3 className="font-semibold mb-2 text-sm uppercase text-gray-500">Categories</h3>
          <ul className="space-y-1 text-sm">
            <li>
              <Link href="/ads" className={`hover:text-blue-600 ${!category ? 'font-bold text-blue-600' : 'text-gray-600'}`}>
                All Categories
              </Link>
            </li>
            {categories.map(cat => (
              <li key={cat}>
                <Link
                  href={`/ads?category=${cat}`}
                  className={`hover:text-blue-600 ${category === cat ? 'font-bold text-blue-600' : 'text-gray-600'}`}
                >
                  {cat}
                </Link>
              </li>
            ))}
          </ul>
        </div>
      </aside>

      {/* Main Results */}
      <div className="flex-1">
        <div className="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <h1 className="text-2xl font-bold">
            {ads.length} Ads {category ? `in ${category}` : (q ? `for "${q}"` : 'found')}
          </h1>
          <div className="flex items-center gap-2 text-sm text-gray-500">
            <span>Sort by:</span>
            <select className="border-none bg-transparent font-semibold text-gray-900 outline-none">
              <option>Newest first</option>
              <option>Price: Low to High</option>
              <option>Price: High to Low</option>
            </select>
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {ads.length > 0 ? (
            ads.map((ad) => (
              <AdCard
                key={ad.id}
                id={ad.id}
                title={ad.title}
                price={ad.price}
                location={ad.location}
                category={ad.category}
                createdAt={ad.createdAt.toISOString()}
                imageUrl={ad.images[0]?.url}
              />
            ))
          ) : (
            <div className="col-span-full py-20 text-center text-gray-500 bg-white border rounded-xl">
              <p className="text-lg">No ads match your search criteria.</p>
              <Link href="/ads" className="text-blue-600 mt-2 font-semibold inline-block">Clear all filters</Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
