'use client';

import { useEffect } from 'react';

interface GoogleAdProps {
  slot: string;
  format?: 'auto' | 'fluid';
  style?: React.CSSProperties;
}

declare global {
  interface Window {
    adsbygoogle: any[];
  }
}

export function GoogleAd({ slot, format = 'auto', style = { display: 'block' } }: GoogleAdProps) {
  useEffect(() => {
    try {
      (window.adsbygoogle = window.adsbygoogle || []).push({});
    } catch (e) {
      console.error('AdSense error', e);
    }
  }, []);

  const publisherId = process.env.NEXT_PUBLIC_GOOGLE_ADSENSE_ID;

  if (!publisherId) return null;

  return (
    <div className="bg-gray-100 rounded-2xl overflow-hidden my-6 border border-gray-200">
      <div className="bg-gray-200 py-1 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">
        Advertisement
      </div>
      <div className="p-4 flex justify-center">
        <ins
          className="adsbygoogle"
          style={style}
          data-ad-client={publisherId}
          data-ad-slot={slot}
          data-ad-format={format}
          data-full-width-responsive="true"
        />
      </div>
    </div>
  );
}
