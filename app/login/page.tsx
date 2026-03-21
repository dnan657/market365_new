'use client';

import { useState } from 'react';
import Link from 'next/link';
import { signIn } from 'next-auth/react';
import ReCAPTCHA from "react-google-recaptcha";

export default function LoginPage() {
  const [captchaValue, setCaptchaValue] = useState<string | null>(null);

  const handleEmailSignIn = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    if (!captchaValue) {
      alert("Please complete the reCAPTCHA");
      return;
    }
    const email = (e.currentTarget.elements.namedItem('email') as HTMLInputElement).value;
    signIn('email', { email, callbackUrl: '/' });
  };

  return (
    <div className="max-w-md mx-auto py-20">
      <div className="bg-white p-10 rounded-[2.5rem] border shadow-2xl shadow-gray-100 space-y-8">
        <div className="text-center space-y-2">
          <h1 className="text-4xl font-black text-gray-900 tracking-tight">Welcome back</h1>
          <p className="text-gray-500 font-medium">Sign in to manage your UK ads</p>
        </div>

        <div className="space-y-4">
          <button
            onClick={() => {
              if (!captchaValue) {
                alert("Please complete the reCAPTCHA");
                return;
              }
              signIn('google', { callbackUrl: '/' });
            }}
            className="w-full flex items-center justify-center gap-3 border-2 border-gray-100 py-4 rounded-2xl font-bold hover:bg-gray-50 transition-all hover:scale-[1.02] active:scale-[0.98]"
          >
            <img src="https://authjs.dev/img/providers/google.svg" alt="Google" className="w-5 h-5" />
            Continue with Google
          </button>

          <div className="relative text-center py-4">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-gray-100"></div>
            </div>
            <span className="relative bg-white px-4 text-xs font-black text-gray-400 uppercase tracking-widest">or email</span>
          </div>

          <form className="space-y-4" onSubmit={handleEmailSignIn}>
            <div className="space-y-2 text-sm">
              <label className="block font-black text-gray-500 uppercase tracking-widest text-[10px]">Email Address</label>
              <input
                name="email"
                type="email"
                required
                placeholder="name@example.co.uk"
                className="w-full border-2 border-gray-100 rounded-2xl p-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all"
              />
            </div>

            <div className="flex justify-center py-2">
               <ReCAPTCHA
                 sitekey={process.env.NEXT_PUBLIC_RECAPTCHA_SITE_KEY || "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"}
                 onChange={(val) => setCaptchaValue(val)}
               />
            </div>

            <button type="submit" className="w-full bg-blue-600 text-white py-4 rounded-2xl font-black text-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
              Send Magic Link
            </button>
          </form>
        </div>

        <p className="text-center text-sm text-gray-500 font-medium pt-4">
          Don't have an account? <Link href="/signup" className="text-blue-600 font-black hover:underline">Join UK Classifieds</Link>
        </p>
      </div>
    </div>
  );
}
