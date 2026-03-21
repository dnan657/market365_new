'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { Shield, Lock, AlertCircle } from 'lucide-react';

export default function AdminLoginPage() {
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const router = useRouter();

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    // In a real app, this would be a secure server-side verification.
    // For this boilerplate, we use a shared secret and a simple cookie.
    if (password === 'uk-admin-2026') {
      document.cookie = "is_admin=true; path=/; max-age=86400";
      router.push('/admin');
    } else {
      setError('Invalid admin password. Access denied.');
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6 -mx-4 -my-8">
      <div className="max-w-md w-full bg-white p-10 rounded-[2.5rem] border shadow-2xl shadow-gray-200 space-y-8">
        <div className="text-center space-y-3">
          <div className="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-blue-200">
            <Shield className="w-8 h-8" />
          </div>
          <h1 className="text-3xl font-black text-gray-900 tracking-tight">Admin Gateway</h1>
          <p className="text-sm text-gray-500 font-medium leading-relaxed">Please enter the security key to access the UK Classifieds moderation suite.</p>
        </div>

        <form onSubmit={handleLogin} className="space-y-6">
          <div className="space-y-2">
            <div className="flex items-center justify-between">
              <label className="text-xs font-black text-gray-400 uppercase tracking-widest">Security Key</label>
              <Lock className="w-4 h-4 text-gray-300" />
            </div>
            <input
              type="password"
              required
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full border-2 border-gray-100 rounded-2xl p-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all font-mono"
              placeholder="••••••••••••"
            />
          </div>

          {error && (
            <div className="flex items-center gap-2 p-4 bg-red-50 text-red-600 rounded-2xl text-xs font-bold border border-red-100">
              <AlertCircle className="w-4 h-4 shrink-0" />
              {error}
            </div>
          )}

          <button type="submit" className="w-full bg-blue-600 text-white py-5 rounded-[1.5rem] font-black text-xl hover:bg-blue-700 transition-all hover:scale-[1.02] active:scale-[0.98] shadow-2xl shadow-blue-200">
            Verify Access
          </button>
        </form>
      </div>
    </div>
  );
}
