import { NextRequest, NextResponse } from 'next/server';
import Stripe from 'stripe';
import prisma from '@/lib/prisma';
import { headers } from 'next/headers';

const stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
  apiVersion: '2025-02-11-preview' as any,
});

export async function POST(req: NextRequest) {
  const body = await req.text();
  const sig = (await headers()).get('stripe-signature')!;

  let event: Stripe.Event;

  try {
    event = stripe.webhooks.constructEvent(body, sig, process.env.STRIPE_WEBHOOK_SECRET!);
  } catch (err: any) {
    console.error('Webhook Error:', err.message);
    return NextResponse.json({ error: err.message }, { status: 400 });
  }

  if (event.type === 'checkout.session.completed') {
    const session = event.data.object as Stripe.Checkout.Session;
    const adId = parseInt(session.metadata?.adId!);
    const userId = parseInt(session.metadata?.userId!);

    // Update Ad Status
    await prisma.ad.update({
      where: { id: adId },
      data: { isPromoted: true }
    });

    // Create Transaction Record
    await prisma.transaction.create({
      data: {
        amount: (session.amount_total || 0) / 100,
        type: 'PROMOTION',
        userId: userId,
        status: 'COMPLETED'
      }
    });

    // Create Notification
    await prisma.notification.create({
      data: {
        userId,
        title: 'Promotion Active!',
        message: 'Your ad has been successfully promoted to the top of results.',
        type: 'PROMOTION'
      }
    });
  }

  return NextResponse.json({ received: true });
}
