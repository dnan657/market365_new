'use server';

import { redirect } from 'next/navigation';
import prisma from '@/lib/prisma';
import { moderateContent } from '@/lib/moderation';
import { getServerSession } from 'next-auth';
import { authOptions } from '@/lib/auth-options';

export async function createAdAction(formData: FormData) {
  const session = await getServerSession(authOptions);

  const title = formData.get('title') as string;
  const subcategoryId = parseInt(formData.get('subcategory') as string);
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

  // 2. Auth User (using session or demo)
  let userId: number;
  if (session?.user) {
    userId = (session.user as any).id;
  } else {
    let user = await prisma.user.findFirst();
    if (!user) {
      user = await prisma.user.create({
        data: { email: 'demo@uk-classifieds.co.uk', name: 'Demo User', password: 'password123' }
      });
    }
    userId = user.id;
  }

  // 3. Extract Dynamic Attributes
  const attributes: { fieldId: number, value: string }[] = [];
  formData.forEach((value, key) => {
    if (key.startsWith('attr-')) {
      const fieldId = parseInt(key.replace('attr-', ''));
      attributes.push({ fieldId, value: value.toString() });
    }
  });

  // 4. Create Ad
  const ad = await prisma.ad.create({
    data: {
      title,
      price,
      location,
      description,
      postcode,
      latitude: lat,
      longitude: lng,
      status,
      authorId: userId,
      subcategoryId,
      categoryName: "General", // Will be derived from subcategory in real usage
      images: {
        create: imageUrls.map(url => ({ url }))
      },
      attributes: {
        create: attributes
      }
    },
  });

  // 5. Log if flagged
  if (!moderation.safe) {
    await prisma.adminLog.create({
      data: { action: 'AUTO_FLAG', details: `Ad ${ad.id} flagged: ${moderation.reason}` }
    });
  }

  redirect(`/ads/${ad.id}`);
}
