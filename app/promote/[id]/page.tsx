import prisma from '@/lib/prisma';
import { notFound } from 'next/navigation';
import PromotePageClient from '@/components/PromotePageClient';

export default async function PromotePage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const adId = parseInt(id);

  if (isNaN(adId)) notFound();

  const ad = await prisma.ad.findUnique({
    where: { id: adId },
  });

  if (!ad) notFound();

  return <PromotePageClient adId={adId} />;
}
