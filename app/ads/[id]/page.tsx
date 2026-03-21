import { notFound } from 'next/navigation';
import Link from 'next/link';
import prisma from '@/lib/prisma';
import { MapPin, Clock, User, Phone, Mail, Share2, Heart, Flag, ShieldCheck } from 'lucide-react';
import AdMap from '@/components/AdMap';
import { GoogleAd } from '@/components/GoogleAd';

export default async function AdDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const adId = parseInt(id);

  if (isNaN(adId)) {
    notFound();
  }

  const ad = await prisma.ad.findUnique({
    where: { id: adId },
    include: { author: true, images: true },
  });

  if (!ad) {
    notFound();
  }

  const mainImage = ad.images[0]?.url || null;

  return (
    <div className="space-y-8">
      {ad.status === 'PENDING' && (
        <div className="bg-yellow-50 border border-yellow-200 p-4 rounded-xl flex items-center gap-3 text-yellow-800 text-sm">
          <Clock className="w-5 h-5" />
          This ad is currently pending moderation and is only visible to you.
        </div>
      )}

      {ad.status === 'FLAGGED' && (
        <div className="bg-red-50 border border-red-200 p-4 rounded-xl flex items-center gap-3 text-red-800 text-sm font-bold">
          <ShieldCheck className="w-5 h-5" />
          Safety Warning: This ad has been automatically flagged for review. Exercise caution.
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Left Column: Images & Description */}
        <div className="lg:col-span-2 space-y-8">
          <div className="bg-gray-100 rounded-2xl aspect-video relative overflow-hidden flex items-center justify-center border shadow-sm">
            {mainImage ? (
              <img
                src={mainImage}
                alt={ad.title}
                className="w-full h-full object-contain bg-white"
              />
            ) : (
              <div className="text-gray-400 flex flex-col items-center">
                <span className="text-4xl mb-2">📷</span>
                No Images Provided
              </div>
            )}
            {ad.images.length > 1 && (
              <div className="absolute bottom-4 right-4 bg-black/60 text-white px-3 py-1 rounded-full text-xs font-bold">
                1 / {ad.images.length} photos
              </div>
            )}
          </div>

          <div className="bg-white p-8 rounded-2xl shadow-sm border space-y-6">
            <div className="space-y-4">
              <div className="flex items-center gap-2 text-sm font-bold text-blue-600 uppercase tracking-widest">
                {ad.category}
              </div>
              <h1 className="text-4xl font-extrabold text-gray-900 tracking-tight">{ad.title}</h1>
              <div className="flex flex-wrap gap-6 text-sm text-gray-500 border-b pb-6">
                <div className="flex items-center gap-1.5">
                  <MapPin className="w-4 h-4" />
                  {ad.location}{ad.postcode ? `, ${ad.postcode}` : ''}
                </div>
                <div className="flex items-center gap-1.5">
                  <Clock className="w-4 h-4" />
                  Posted {new Date(ad.createdAt).toLocaleDateString('en-GB')}
                </div>
                <div className="flex items-center gap-1.5">
                  ID: #{ad.id}
                </div>
              </div>
            </div>

            <div className="space-y-4">
              <h2 className="text-2xl font-bold text-gray-900">Description</h2>
              <p className="text-gray-700 whitespace-pre-wrap leading-relaxed text-lg">
                {ad.description}
              </p>
            </div>

            <div className="flex items-center gap-6 pt-8 border-t">
              <button className="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-bold transition-colors">
                <Share2 className="w-5 h-5" /> Share
              </button>
              <button className="flex items-center gap-2 text-gray-600 hover:text-red-600 font-bold transition-colors">
                <Heart className="w-5 h-5" /> Save
              </button>
              <button className="flex items-center gap-2 text-gray-600 hover:text-orange-600 font-bold transition-colors ml-auto">
                <Flag className="w-5 h-5" /> Report
              </button>
            </div>
          </div>

          {/* AdSense Unit */}
          <GoogleAd slot="1234567890" />

          <div className="space-y-4">
            <h2 className="text-2xl font-bold text-gray-900">Location Map</h2>
            <AdMap
              lat={ad.latitude || 51.5074}
              lng={ad.longitude || -0.1278}
              locationName={ad.location}
            />
          </div>
        </div>

        {/* Right Column: Price & Contact */}
        <div className="space-y-6">
          <div className="bg-white p-8 rounded-2xl shadow-sm border space-y-8 sticky top-24">
            <div className="space-y-1">
              <p className="text-gray-500 text-xs font-bold uppercase tracking-widest">Asking Price</p>
              <p className="text-5xl font-black text-blue-800">£{ad.price.toLocaleString()}</p>
            </div>

            <div className="space-y-4">
              <button className="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-blue-200">
                <Phone className="w-6 h-6" /> Show Phone Number
              </button>
              <button className="w-full bg-white border-2 border-blue-600 text-blue-600 py-4 rounded-2xl font-bold text-lg flex items-center justify-center gap-2 hover:bg-blue-50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <Mail className="w-6 h-6" /> Message Seller
              </button>
            </div>

            <div className="pt-8 border-t">
              <div className="flex items-center gap-4 mb-6">
                <div className="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400 font-black text-xl border shadow-inner">
                  {ad.author.name?.[0] || 'U'}
                </div>
                <div>
                  <p className="font-black text-lg text-gray-900">{ad.author.name || 'Anonymous User'}</p>
                  <p className="text-sm text-gray-500 font-medium">Member since {new Date(ad.author.createdAt).getFullYear()}</p>
                </div>
              </div>
              <Link href={`/users/${ad.authorId}/ads`} className="inline-block w-full text-center bg-gray-50 text-blue-600 py-3 rounded-xl font-bold hover:bg-blue-50 transition-colors border">
                View all seller's ads
              </Link>
            </div>

            {/* Sidebar Ad Unit */}
            <GoogleAd slot="0987654321" format="fluid" />
          </div>

          <div className="bg-yellow-50 p-8 rounded-2xl border border-yellow-200 shadow-sm">
            <h3 className="font-bold text-yellow-800 mb-4 flex items-center gap-2 text-lg">
              🛡️ UK Safety Guide
            </h3>
            <ul className="text-sm text-yellow-700 space-y-3 list-disc ml-5 font-medium">
              <li>Never pay for an item before seeing it in person.</li>
              <li>Always meet in a well-lit, public place.</li>
              <li>Don't provide personal details (bank login, etc.)</li>
              <li>If a deal sounds too good to be true, it probably is.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
