import Link from 'next/link';

interface CategoryCardProps {
  name: string;
  slug: string;
  icon?: React.ReactNode;
  count?: number;
}

export function CategoryCard({ name, slug, icon, count }: CategoryCardProps) {
  return (
    <Link
      href={`/ads?category=${slug}`}
      className="flex flex-col items-center justify-center p-6 border rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-all text-center group"
    >
      <div className="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
        {icon}
      </div>
      <h3 className="font-bold text-gray-800 text-sm">{name}</h3>
      {count !== undefined && (
        <span className="text-xs text-gray-500 mt-1">{count} ads</span>
      )}
    </Link>
  );
}
