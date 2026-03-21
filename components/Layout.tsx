'use client';

import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { Search, PlusCircle, User, MapPin, MessageSquare, LogOut } from 'lucide-react';
import { useSession, signOut } from 'next-auth/react';
import { NotificationCenter } from './NotificationCenter';

export function Header() {
  const router = useRouter();
  const { data: session } = useSession();

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
    <header className="border-b bg-white sticky top-0 z-50">
      <div className="container mx-auto px-4 h-20 flex items-center justify-between gap-8">
        <Link href="/" className="text-2xl font-black text-blue-600 flex items-center gap-2 shrink-0">
          <span className="bg-blue-600 text-white px-2 py-1 rounded-lg shadow-lg shadow-blue-100">UK</span>
          <span className="hidden sm:inline tracking-tighter">Classifieds</span>
        </Link>

        <form onSubmit={handleSearch} className="flex-1 max-w-2xl hidden md:flex items-center gap-3 border-2 border-gray-100 rounded-2xl px-6 py-2.5 bg-gray-50/50 focus-within:border-blue-500 focus-within:bg-white transition-all">
          <Search className="w-5 h-5 text-gray-400" />
          <input
            name="q"
            type="text"
            placeholder="Search across the UK..."
            className="bg-transparent border-none outline-none flex-1 font-medium text-gray-700 placeholder:text-gray-400"
          />
          <div className="h-6 w-px bg-gray-200 mx-2" />
          <div className="flex items-center gap-2 text-gray-500 whitespace-nowrap font-bold text-xs uppercase tracking-widest">
            <MapPin className="w-4 h-4 text-blue-500" />
            <span>UK Wide</span>
          </div>
          <button type="submit" className="bg-blue-600 text-white px-6 py-2 rounded-xl text-sm font-black hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 active:scale-95">
            Search
          </button>
        </form>

        <div className="flex items-center gap-2 sm:gap-6">
          {session ? (
            <>
              <div className="flex items-center gap-2 sm:gap-4 border-r pr-4 sm:pr-6 border-gray-100">
                <NotificationCenter />
                <Link href="/chats" className="p-2 text-gray-500 hover:text-blue-600 transition-colors relative">
                  <MessageSquare className="w-6 h-6" />
                </Link>
              </div>
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black border border-blue-100 shadow-sm overflow-hidden">
                  {session.user?.image ? (
                    <img src={session.user.image} alt={session.user.name || ''} className="w-full h-full object-cover" />
                  ) : (
                    session.user?.name?.[0] || 'U'
                  )}
                </div>
                <button
                  onClick={() => signOut()}
                  className="p-2 text-gray-400 hover:text-red-600 transition-colors"
                  title="Sign Out"
                >
                  <LogOut className="w-5 h-5" />
                </button>
              </div>
            </>
          ) : (
            <Link href="/login" className="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-bold text-sm transition-colors">
              <User className="w-5 h-5" />
              <span className="hidden sm:inline">Sign In</span>
            </Link>
          )}

          <Link href="/ads/create" className="flex items-center gap-2 bg-yellow-400 text-blue-900 px-4 sm:px-6 py-3 rounded-2xl font-black text-sm hover:bg-yellow-500 transition-all hover:scale-105 active:scale-95 shadow-xl shadow-yellow-100">
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
    <footer className="bg-white border-t mt-20 py-20">
      <div className="container mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
        <div className="space-y-6">
           <Link href="/" className="text-2xl font-black text-blue-600 flex items-center gap-2">
            <span className="bg-blue-600 text-white px-2 py-1 rounded-lg">UK</span>
            Classifieds
          </Link>
          <p className="text-gray-500 text-sm font-medium leading-relaxed">
            The UK's most trusted local marketplace. Buy and sell almost anything in your community.
          </p>
        </div>
        <div>
          <h3 className="font-black text-gray-900 mb-6 uppercase tracking-widest text-xs">Platform</h3>
          <ul className="space-y-4 text-sm font-bold text-gray-500">
            <li><Link href="/ads" className="hover:text-blue-600">Browse Ads</Link></li>
            <li><Link href="/pricing" className="hover:text-blue-600">Pricing & Plans</Link></li>
            <li><Link href="/admin" className="hover:text-blue-600">Admin Portal</Link></li>
          </ul>
        </div>
        <div>
          <h3 className="font-black text-gray-900 mb-6 uppercase tracking-widest text-xs">Support</h3>
          <ul className="space-y-4 text-sm font-bold text-gray-500">
            <li><Link href="/support" className="hover:text-blue-600">Help Center</Link></li>
            <li><Link href="/safety" className="hover:text-blue-600">Safety Tips</Link></li>
            <li><Link href="/rules" className="hover:text-blue-600">Posting Rules</Link></li>
          </ul>
        </div>
        <div>
          <h3 className="font-black text-gray-900 mb-6 uppercase tracking-widest text-xs">Legal</h3>
          <ul className="space-y-4 text-sm font-bold text-gray-500">
            <li><Link href="/terms" className="hover:text-blue-600">Terms of Use</Link></li>
            <li><Link href="/privacy" className="hover:text-blue-600">Privacy Policy</Link></li>
          </ul>
        </div>
      </div>
      <div className="container mx-auto px-4 mt-20 pt-10 border-t flex flex-col sm:flex-row justify-between items-center gap-6">
        <p className="text-xs font-bold text-gray-400 uppercase tracking-widest">
          © {new Date().getFullYear()} UK Classifieds Ltd. All rights reserved.
        </p>
        <div className="flex gap-4">
           {/* Social links placeholder */}
        </div>
      </div>
    </footer>
  );
}
