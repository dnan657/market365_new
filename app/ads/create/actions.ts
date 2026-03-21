'use server';

import { redirect } from 'next/navigation';
import prisma from '@/lib/prisma';
import { moderateContent } from '@/lib/moderation';

export async function createAdAction(formData: FormData) {
  const title = formData.get('title') as string;
  const category = formData.get('category') as string;
  const price = parseFloat(formData.get('price') as string);
  const location = formData.get('location') as string;
  const description = formData.get('description') as string;
  const postcode = formData.get('postcode') as string;
  const lat = parseFloat(formData.get('lat') as string) || 51.5074;
  const lng = parseFloat(formData.get('lng') as string) || -0.1278;
  const imageUrls = formData.getAll('imageUrls') as string[];

  // 1. Run AI Moderation
  const moderation = await moderateContent(title, description);
  const status = moderation.safe ? "PENDING" : "FLAGGED";

  // 2. Fetch or create demo author
  let user = await prisma.user.findFirst();
  if (!user) {
    user = await prisma.user.create({
      data: {
        email: 'demo@uk-classifieds.co.uk',
        name: 'Demo User',
        password: 'password123',
      }
    });
  }

  // 3. Create Ad with status and location details
  const ad = await prisma.ad.create({
    data: {
      title,
      category,
      price,
      location,
      description,
      postcode,
      latitude: lat,
      longitude: lng,
      status,
      authorId: user.id,
      images: {
        create: imageUrls.map(url => ({ url }))
      }
    },
  });

  // 4. Log admin action if flagged
  if (!moderation.safe) {
    await prisma.adminLog.create({
      data: {
        action: 'AUTO_FLAG',
        details: `Ad ${ad.id} flagged for reason: ${moderation.reason}`
      }
    });
  }

  redirect(`/ads/${ad.id}`);
}
