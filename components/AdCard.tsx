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
  isPromoted?: boolean;
}

export function AdCard({ id, title, price, location, imageUrl, category, createdAt, isPromoted }: AdCardProps) {
  return (
    <Link href={`/ads/${id}`} className={`group border rounded-2xl overflow-hidden hover:shadow-xl transition-all bg-white relative ${isPromoted ? 'ring-2 ring-blue-600 shadow-blue-50' : ''}`}>
      {isPromoted && (
        <div className="absolute top-2 right-2 z-10 bg-blue-600 text-white text-[10px] font-black px-2 py-1 rounded-lg shadow-lg uppercase tracking-tighter">
          Featured
        </div>
      )}
      <div className="relative aspect-[4/3] bg-gray-50">
        {imageUrl ? (
          <img
            src={imageUrl}
            alt={title}
            className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
          />
        ) : (
          <div className="w-full h-full flex flex-col items-center justify-center text-gray-300 gap-2">
            <span className="text-2xl">📷</span>
            <span className="text-[10px] font-bold uppercase tracking-widest">No Photos</span>
          </div>
        )}
        <div className="absolute bottom-2 left-2 bg-black/70 text-white text-[9px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider backdrop-blur-sm">
          {category}
        </div>
      </div>
      <div className="p-4 space-y-2">
        <h3 className="font-bold text-gray-900 group-hover:text-blue-600 line-clamp-2 min-h-[3rem] tracking-tight">
          {title}
        </h3>
        <div className="flex items-baseline gap-1">
          <span className="text-xl font-black text-blue-800">£{price.toLocaleString()}</span>
        </div>
        <div className="flex items-center gap-1 text-[10px] font-bold text-gray-400 pt-2 border-t uppercase tracking-widest">
          <MapPin className="w-3 h-3 text-blue-500" />
          <span className="truncate">{location}</span>
        </div>
        <div className="flex items-center gap-1 text-[10px] font-medium text-gray-400">
          <Clock className="w-3 h-3" />
          <span>{new Date(createdAt).toLocaleDateString('en-GB')}</span>
        </div>
      </div>
    </Link>
  );
}
