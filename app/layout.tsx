import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import { Header, Footer } from "@/components/Layout";
import { Providers } from "@/components/Providers";
import Script from "next/script";
import { PushNotificationManager } from "@/lib/push";

const inter = Inter({ subsets: ["latin"] });

export const metadata: Metadata = {
  title: "UK Classifieds | Buy & Sell Locally",
  description: "The best place to buy and sell used goods in the UK.",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const adsenseId = process.env.NEXT_PUBLIC_GOOGLE_ADSENSE_ID;

  return (
    <html lang="en">
      <head>
        {adsenseId && (
          <Script
            async
            src={`https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=${adsenseId}`}
            crossOrigin="anonymous"
            strategy="lazyOnload"
          />
        )}
      </head>
      <body className={`${inter.className} min-h-screen flex flex-col bg-gray-50/30`}>
        <Providers>
          <PushNotificationManager />
          <Header />
          <main className="flex-1 container mx-auto px-4 py-8">
            {children}
          </main>
          <Footer />
        </Providers>
      </body>
    </html>
  );
}
