'use client';

import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { Search, PlusCircle, User, MapPin } from 'lucide-react';

export function Header() {
  const router = useRouter();

  const handleSearch = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const q = formData.get('q');
    if (q) {
      router.push(`/ads?q=${encodeURIComponent(q.toString())}`);
    } else {
      router.push('/ads');
    }
  };

  return (
    <header className="border-b bg-white">
      <div className="container mx-auto px-4 h-16 flex items-center justify-between gap-4">
        <Link href="/" className="text-2xl font-bold text-blue-600 flex items-center gap-2">
          <span className="bg-blue-600 text-white px-2 py-1 rounded">UK</span>
          Classifieds
        </Link>

        <form onSubmit={handleSearch} className="flex-1 max-w-2xl hidden md:flex items-center gap-2 border rounded-full px-4 py-2 bg-gray-50">
          <Search className="w-5 h-5 text-gray-400" />
          <input
            name="q"
            type="text"
            placeholder="Search for anything..."
            className="bg-transparent border-none outline-none flex-1"
          />
          <div className="h-4 w-px bg-gray-300 mx-2" />
          <div className="flex items-center gap-1 text-gray-600 whitespace-nowrap">
            <MapPin className="w-4 h-4" />
            <span>UK Wide</span>
          </div>
          <button type="submit" className="bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-blue-700">
            Search
          </button>
        </form>

        <div className="flex items-center gap-4">
          <Link href="/login" className="flex items-center gap-1 text-gray-600 hover:text-blue-600">
            <User className="w-5 h-5" />
            <span className="hidden sm:inline">Sign In</span>
          </Link>
          <Link href="/ads/create" className="flex items-center gap-1 bg-yellow-400 text-blue-900 px-4 py-2 rounded-full font-bold hover:bg-yellow-500 transition-colors">
            <PlusCircle className="w-5 h-5" />
            <span className="hidden sm:inline">Post an Ad</span>
          </Link>
        </div>
      </div>
    </header>
  );
}

export function Footer() {
  return (
    <footer className="bg-gray-100 border-t mt-12 py-12">
      <div className="container mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-8">
        <div>
          <h3 className="font-bold mb-4">About Us</h3>
          <ul className="space-y-2 text-sm text-gray-600">
            <li>About UK Classifieds</li>
            <li>Press Room</li>
            <li>Work with Us</li>
          </ul>
        </div>
        <div>
          <h3 className="font-bold mb-4">Support</h3>
          <ul className="space-y-2 text-sm text-gray-600">
            <li>Help & Contact</li>
            <li>Safety Tips</li>
            <li>Rules & Regs</li>
          </ul>
        </div>
        <div>
          <h3 className="font-bold mb-4">Legal</h3>
          <ul className="space-y-2 text-sm text-gray-600">
            <li>Terms of Use</li>
            <li>Privacy Policy</li>
            <li>Cookie Policy</li>
          </ul>
        </div>
        <div>
          <h3 className="font-bold mb-4">Mobile</h3>
          <div className="flex flex-col gap-2">
            <div className="bg-black text-white px-4 py-2 rounded text-xs">App Store</div>
            <div className="bg-black text-white px-4 py-2 rounded text-xs">Google Play</div>
          </div>
        </div>
      </div>
      <div className="container mx-auto px-4 mt-12 pt-8 border-t text-center text-sm text-gray-500">
        © {new Date().getFullYear()} UK Classifieds. All rights reserved.
      </div>
    </footer>
  );
}
