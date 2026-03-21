import { notFound } from 'next/navigation';
import Link from 'next/link';
import prisma from '@/lib/prisma';
import { MapPin, Clock, User, Phone, Mail, Share2, Heart, Flag } from 'lucide-react';

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
    include: { author: true },
  });

  if (!ad) {
    notFound();
  }

  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
      {/* Left Column: Images & Description */}
      <div className="lg:col-span-2 space-y-6">
        <div className="bg-gray-100 rounded-2xl aspect-video relative overflow-hidden flex items-center justify-center">
          {ad.imageUrl ? (
            <img
              src={ad.imageUrl}
              alt={ad.title}
              className="w-full h-full object-contain"
            />
          ) : (
            <div className="text-gray-400 flex flex-col items-center">
              <span className="text-4xl mb-2">📷</span>
              No Images Provided
            </div>
          )}
        </div>

        <div className="bg-white p-6 rounded-2xl shadow-sm border space-y-4">
          <div className="flex items-center gap-2 text-sm font-medium text-blue-600 mb-2 uppercase tracking-wide">
            {ad.category}
          </div>
          <h1 className="text-3xl font-bold text-gray-900">{ad.title}</h1>
          <div className="flex flex-wrap gap-4 text-sm text-gray-500 border-b pb-4">
            <div className="flex items-center gap-1">
              <MapPin className="w-4 h-4" />
              {ad.location}
            </div>
            <div className="flex items-center gap-1">
              <Clock className="w-4 h-4" />
              Posted {new Date(ad.createdAt).toLocaleDateString('en-GB')}
            </div>
            <div className="flex items-center gap-1">
              ID: {ad.id}
            </div>
          </div>

          <div>
            <h2 className="text-xl font-bold mb-3">Description</h2>
            <p className="text-gray-700 whitespace-pre-wrap leading-relaxed">
              {ad.description}
            </p>
          </div>

          <div className="flex items-center gap-6 pt-6 border-t">
            <button className="flex items-center gap-1 text-gray-600 hover:text-blue-600 text-sm font-medium">
              <Share2 className="w-4 h-4" /> Share
            </button>
            <button className="flex items-center gap-1 text-gray-600 hover:text-red-600 text-sm font-medium">
              <Heart className="w-4 h-4" /> Save
            </button>
            <button className="flex items-center gap-1 text-gray-600 hover:text-orange-600 text-sm font-medium ml-auto">
              <Flag className="w-4 h-4" /> Report
            </button>
          </div>
        </div>
      </div>

      {/* Right Column: Price & Contact */}
      <div className="space-y-6">
        <div className="bg-white p-6 rounded-2xl shadow-sm border space-y-6 sticky top-8">
          <div className="space-y-1">
            <p className="text-gray-500 text-sm font-medium uppercase">Price</p>
            <p className="text-4xl font-extrabold text-blue-800">£{ad.price.toLocaleString()}</p>
          </div>

          <div className="space-y-3">
            <button className="w-full bg-blue-600 text-white py-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-blue-700 transition-colors">
              <Phone className="w-5 h-5" /> Reveal Phone Number
            </button>
            <button className="w-full bg-white border-2 border-blue-600 text-blue-600 py-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-blue-50 transition-colors">
              <Mail className="w-5 h-5" /> Message Seller
            </button>
          </div>

          <div className="pt-6 border-t">
            <div className="flex items-center gap-4 mb-4">
              <div className="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold">
                {ad.author.name?.[0] || 'U'}
              </div>
              <div>
                <p className="font-bold">{ad.author.name || 'Anonymous User'}</p>
                <p className="text-xs text-gray-500">Member since {new Date(ad.author.createdAt).getFullYear()}</p>
              </div>
            </div>
            <Link href={`/users/${ad.authorId}/ads`} className="text-blue-600 text-sm font-bold hover:underline">
              View seller's other ads
            </Link>
          </div>
        </div>

        <div className="bg-yellow-50 p-6 rounded-2xl border border-yellow-200">
          <h3 className="font-bold text-yellow-800 mb-2 flex items-center gap-2">
            🛡️ Safety First
          </h3>
          <ul className="text-xs text-yellow-700 space-y-2 list-disc ml-4">
            <li>Never pay in advance for an item.</li>
            <li>Meet in a safe, public place.</li>
            <li>Inspect the item before you buy.</li>
            <li>Check our full safety guide for more tips.</li>
          </ul>
        </div>
      </div>
    </div>
  );
}
