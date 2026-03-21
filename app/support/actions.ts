'use server';

import prisma from '@/lib/prisma';
import { revalidatePath } from 'next/cache';

export async function createTicketAction(formData: FormData) {
  const subject = formData.get('subject') as string;
  const message = formData.get('message') as string;
  const email = formData.get('email') as string;
  const name = formData.get('name') as string;

  // Fetch or create demo user
  let user = await prisma.user.findFirst({ where: { email } });
  if (!user) {
    user = await prisma.user.create({
      data: {
        email,
        name,
        password: 'password123',
      }
    });
  }

  await prisma.ticket.create({
    data: {
      subject,
      message,
      userId: user.id,
      status: 'OPEN'
    }
  });

  revalidatePath('/admin/tickets');
  return { success: true };
}
