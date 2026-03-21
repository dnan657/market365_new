import prisma from '@/lib/prisma';
import { AdCard } from '@/components/AdCard';
import { Search, MapPin, SlidersHorizontal, ArrowUpDown } from 'lucide-react';
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
  const sort = typeof params.sort === 'string' ? params.sort : 'newest';

  const orderBy: any = {};
  if (sort === 'price_asc') orderBy.price = 'asc';
  else if (sort === 'price_desc') orderBy.price = 'desc';
  else orderBy.createdAt = 'desc';

  const ads = await prisma.ad.findMany({
    where: {
      AND: [
        category ? { category: { contains: category, mode: 'insensitive' } } : {},
        q ? {
          OR: [
            { title: { contains: q, mode: 'insensitive' } },
            { description: { contains: q, mode: 'insensitive' } },
          ]
        } : {},
        location ? { location: { contains: location, mode: 'insensitive' } } : {},
        minPrice ? { price: { gte: minPrice } } : {},
        maxPrice ? { price: { lte: maxPrice } } : {},
        { status: 'APPROVED' }
      ]
    },
    include: { images: true },
    orderBy: [
      { isPromoted: 'desc' },
      orderBy
    ],
  });

  const locations = ["London", "Manchester", "Birmingham", "Leeds", "Glasgow", "Bristol", "Edinburgh"];
  const categories = ["Cars & Vehicles", "Property", "Electronics", "Home & Garden", "Pets", "Jobs"];

  return (
    <div className="flex flex-col lg:flex-row gap-12 py-6">
      {/* Sidebar Filters */}
      <aside className="w-full lg:w-80 space-y-10">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-3 font-black text-2xl text-gray-900">
            <SlidersHorizontal className="w-6 h-6 text-blue-600" />
            <h2>Filters</h2>
          </div>
          <Link href="/ads" className="text-xs font-black text-blue-600 uppercase tracking-widest hover:underline">Reset</Link>
        </div>

        <div className="space-y-6">
          <div className="space-y-4">
            <h3 className="font-black text-xs uppercase tracking-[0.2em] text-gray-400">UK Region</h3>
            <div className="grid grid-cols-2 gap-2">
              {locations.map(loc => (
                <Link
                  key={loc}
                  href={`/ads?location=${loc}${category ? `&category=${category}` : ''}${q ? `&q=${q}` : ''}`}
                  className={`px-4 py-2.5 rounded-xl text-xs font-bold transition-all border-2 text-center ${location?.toLowerCase() === loc.toLowerCase() ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-white border-gray-100 text-gray-500 hover:border-blue-200'}`}
                >
                  {loc}
                </Link>
              ))}
            </div>
          </div>

          <div className="space-y-4">
            <h3 className="font-black text-xs uppercase tracking-[0.2em] text-gray-400">Categories</h3>
            <div className="flex flex-wrap gap-2">
              {categories.map(cat => (
                <Link
                  key={cat}
                  href={`/ads?category=${cat}${location ? `&location=${location}` : ''}${q ? `&q=${q}` : ''}`}
                  className={`px-4 py-2.5 rounded-xl text-xs font-bold transition-all border-2 ${category === cat ? 'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-white border-gray-100 text-gray-500 hover:border-blue-200'}`}
                >
                  {cat}
                </Link>
              ))}
            </div>
          </div>

          <div className="space-y-4 pt-4 border-t border-gray-100">
            <h3 className="font-black text-xs uppercase tracking-[0.2em] text-gray-400">Price Range (£)</h3>
            <div className="flex gap-4">
              <input type="number" placeholder="Min" className="w-full border-2 border-gray-100 rounded-xl p-3 text-sm font-bold outline-none focus:border-blue-500" />
              <input type="number" placeholder="Max" className="w-full border-2 border-gray-100 rounded-xl p-3 text-sm font-bold outline-none focus:border-blue-500" />
            </div>
          </div>
        </div>
      </aside>

      {/* Main Results */}
      <div className="flex-1 space-y-8">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-6 bg-white p-6 rounded-[2rem] border shadow-sm">
          <div>
            <h1 className="text-3xl font-black text-gray-900 tracking-tight">
              {ads.length} {ads.length === 1 ? 'Result' : 'Results'}
            </h1>
            <p className="text-gray-500 font-medium text-sm">
              {category ? `Browsing ${category}` : (q ? `Searching for "${q}"` : 'Found across the UK')}
            </p>
          </div>

          <div className="flex items-center gap-3">
             <ArrowUpDown className="w-4 h-4 text-gray-400" />
             <select className="bg-gray-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-gray-900 outline-none cursor-pointer hover:bg-gray-100 transition-colors">
                <option value="newest">Newest first</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
             </select>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
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
                isPromoted={ad.isPromoted}
              />
            ))
          ) : (
            <div className="col-span-full py-32 text-center space-y-4 bg-white border-4 border-dashed rounded-[3rem]">
              <Search className="w-16 h-16 mx-auto text-gray-200" />
              <div className="space-y-1">
                <p className="text-2xl font-black text-gray-900">No matches found</p>
                <p className="text-gray-500 font-medium max-w-xs mx-auto">Try adjusting your filters or search terms to find what you're looking for.</p>
              </div>
              <Link href="/ads" className="inline-block bg-blue-600 text-white px-8 py-3 rounded-2xl font-black shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Clear Search</Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
