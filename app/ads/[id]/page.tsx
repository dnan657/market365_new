import { notFound } from 'next/navigation';
import Link from 'next/link';
import prisma from '@/lib/prisma';
import { MapPin, Clock, User, Phone, Mail, Share2, Heart, Flag, ShieldCheck, ListChecks } from 'lucide-react';
import AdMap from '@/components/AdMap';
import { GoogleAd } from '@/components/GoogleAd';

export default async function AdDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const adId = parseInt(id);

  if (isNaN(adId)) notFound();

  const ad = await prisma.ad.findUnique({
    where: { id: adId },
    include: {
      author: true,
      images: true,
      subcategory: { include: { category: true } },
      attributes: { include: { field: true } }
    },
  });

  if (!ad) notFound();

  const mainImage = ad.images[0]?.url || null;

  return (
    <div className="space-y-8 py-6">
      {/* Breadcrumbs */}
      <nav className="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-400">
        <Link href="/" className="hover:text-blue-600 transition-colors">Home</Link>
        <span>/</span>
        <Link href={`/ads?category=${ad.subcategory.category.name}`} className="hover:text-blue-600 transition-colors">{ad.subcategory.category.name}</Link>
        <span>/</span>
        <span className="text-gray-900">{ad.subcategory.name}</span>
      </nav>

      {ad.status === 'PENDING' && (
        <div className="bg-yellow-50 border-2 border-yellow-100 p-6 rounded-3xl flex items-center gap-4 text-yellow-800 text-sm font-bold shadow-sm shadow-yellow-50">
          <div className="p-2 bg-yellow-100 rounded-xl"><Clock className="w-5 h-5" /></div>
          This listing is currently awaiting AI and Moderator approval.
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {/* Left Column */}
        <div className="lg:col-span-2 space-y-12">
          {/* Gallery */}
          <div className="bg-white rounded-[3rem] aspect-video relative overflow-hidden border-2 border-gray-50 shadow-xl group">
            {mainImage ? (
              <img src={mainImage} alt={ad.title} className="w-full h-full object-contain" />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-gray-300">No Image</div>
            )}
            <div className="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
              {ad.images.map((img, i) => (
                 <div key={i} className={`w-2 h-2 rounded-full ${i === 0 ? 'bg-blue-600' : 'bg-gray-200'}`} />
              ))}
            </div>
          </div>

          <div className="bg-white p-10 rounded-[3rem] border shadow-sm space-y-10">
            <div className="space-y-6">
              <h1 className="text-5xl font-black text-gray-900 tracking-tight leading-tight">{ad.title}</h1>
              <div className="flex flex-wrap gap-8 text-xs font-bold text-gray-400 uppercase tracking-[0.1em] border-b pb-8">
                <div className="flex items-center gap-2"><MapPin className="w-4 h-4 text-blue-500" /> {ad.location} {ad.postcode}</div>
                <div className="flex items-center gap-2"><Clock className="w-4 h-4 text-blue-500" /> {new Date(ad.createdAt).toLocaleDateString('en-GB')}</div>
                <div className="flex items-center gap-2">ID #{ad.id}</div>
              </div>
            </div>

            {/* Dynamic Attributes */}
            {ad.attributes.length > 0 && (
              <div className="space-y-6">
                <div className="flex items-center gap-3">
                  <div className="p-2 bg-blue-50 text-blue-600 rounded-xl"><ListChecks className="w-5 h-5" /></div>
                  <h2 className="text-2xl font-black text-gray-900 tracking-tight">Specifications</h2>
                </div>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-6">
                  {ad.attributes.map((attr) => (
                    <div key={attr.id} className="bg-gray-50/50 p-4 rounded-2xl border border-gray-100 space-y-1">
                      <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{attr.field.name}</p>
                      <p className="text-sm font-black text-gray-900 tracking-tight">{attr.value}</p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            <div className="space-y-6">
              <h2 className="text-2xl font-black text-gray-900 tracking-tight">Description</h2>
              <p className="text-gray-600 whitespace-pre-wrap leading-relaxed text-lg font-medium italic">
                {ad.description}
              </p>
            </div>

            <div className="flex items-center gap-8 pt-10 border-t">
              <button className="flex items-center gap-2 text-sm font-black text-gray-400 hover:text-blue-600 transition-colors uppercase tracking-widest"><Share2 className="w-4 h-4" /> Share</button>
              <button className="flex items-center gap-2 text-sm font-black text-gray-400 hover:text-red-600 transition-colors uppercase tracking-widest"><Heart className="w-4 h-4" /> Save</button>
              <button className="ml-auto flex items-center gap-2 text-sm font-black text-gray-400 hover:text-orange-600 transition-colors uppercase tracking-widest"><Flag className="w-4 h-4" /> Report</button>
            </div>
          </div>

          <GoogleAd slot="1234567890" />

          <div className="space-y-6">
            <h2 className="text-2xl font-black text-gray-900 tracking-tight">Meeting Location</h2>
            <AdMap lat={ad.latitude || 51.5074} lng={ad.longitude || -0.1278} locationName={ad.location} />
          </div>
        </div>

        {/* Right Column */}
        <div className="space-y-8">
          <div className="bg-white p-10 rounded-[3rem] border shadow-2xl shadow-blue-50 space-y-10 sticky top-24">
            <div className="space-y-2 text-center">
              <p className="text-gray-400 text-xs font-black uppercase tracking-[0.2em]">Listing Price</p>
              <p className="text-6xl font-black text-blue-800 tracking-tighter">£{ad.price.toLocaleString()}</p>
            </div>

            <div className="space-y-4">
              <button className="w-full bg-blue-600 text-white py-5 rounded-[1.5rem] font-black text-xl flex items-center justify-center gap-3 hover:bg-blue-700 transition-all hover:scale-[1.02] active:scale-[0.98] shadow-2xl shadow-blue-100">
                <Phone className="w-6 h-6" /> Show Number
              </button>
              <button className="w-full bg-white border-2 border-blue-600 text-blue-600 py-5 rounded-[1.5rem] font-black text-xl flex items-center justify-center gap-3 hover:bg-blue-50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <Mail className="w-6 h-6" /> Send Message
              </button>
            </div>

            <div className="pt-10 border-t space-y-6">
              <div className="flex items-center gap-4">
                <div className="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-black text-2xl border shadow-inner">
                  {ad.author.name?.[0] || 'U'}
                </div>
                <div>
                  <p className="font-black text-xl text-gray-900 tracking-tight">{ad.author.name}</p>
                  <p className="text-xs text-gray-400 font-bold uppercase tracking-widest">Member since {new Date(ad.author.createdAt).getFullYear()}</p>
                </div>
              </div>
              <Link href={`/users/${ad.authorId}/ads`} className="inline-block w-full text-center bg-gray-50 text-blue-600 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-blue-50 transition-colors border-2 border-dashed">
                Seller's listings
              </Link>
            </div>

            <GoogleAd slot="0987654321" format="fluid" />
          </div>

          <div className="bg-yellow-50/50 p-10 rounded-[3rem] border-2 border-yellow-100/50">
             <h3 className="font-black text-yellow-800 mb-6 flex items-center gap-2 text-xl tracking-tight">🛡️ UK Safety Guide</h3>
             <ul className="space-y-4 text-sm font-bold text-yellow-700/80 italic">
               <li className="flex gap-3"><span>•</span> Never pay before inspection</li>
               <li className="flex gap-3"><span>•</span> Meet in daylight, public area</li>
               <li className="flex gap-3"><span>•</span> Use secure payment methods</li>
             </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
