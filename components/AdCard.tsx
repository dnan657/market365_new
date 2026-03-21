import Link from 'next/link';
import { MapPin, Clock } from 'lucide-react';

interface AdCardProps {
  id: number;
  title: string;
  price: number;
  location: string;
  imageUrl?: string | null;
  category: string;
  createdAt: string;
}

export function AdCard({ id, title, price, location, imageUrl, category, createdAt }: AdCardProps) {
  return (
    <Link href={`/ads/${id}`} className="group border rounded-lg overflow-hidden hover:shadow-lg transition-shadow bg-white">
      <div className="relative aspect-[4/3] bg-gray-200">
        {imageUrl ? (
          <img
            src={imageUrl}
            alt={title}
            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
          />
        ) : (
          <div className="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
            No Image
          </div>
        )}
        <div className="absolute top-2 left-2 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded font-medium uppercase tracking-wider">
          {category}
        </div>
      </div>
      <div className="p-4 space-y-2">
        <h3 className="font-semibold text-gray-900 group-hover:text-blue-600 line-clamp-2 min-h-[3rem]">
          {title}
        </h3>
        <div className="flex items-baseline gap-1">
          <span className="text-xl font-extrabold text-blue-800">£{price.toLocaleString()}</span>
        </div>
        <div className="flex items-center gap-1 text-xs text-gray-500 pt-2 border-t">
          <MapPin className="w-3 h-3" />
          <span className="truncate">{location}</span>
        </div>
        <div className="flex items-center gap-1 text-[10px] text-gray-400">
          <Clock className="w-3 h-3" />
          <span>{new Date(createdAt).toLocaleDateString('en-GB')}</span>
        </div>
      </div>
    </Link>
  );
}
