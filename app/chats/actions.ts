'use server';

import prisma from '@/lib/prisma';
import { getServerSession } from 'next-auth';
import { authOptions } from '@/lib/auth-options';
import { revalidatePath } from 'next/cache';

export async function sendMessageAction(chatId: string, content: string, receiverId: number) {
  const session = await getServerSession(authOptions);
  if (!session?.user) throw new Error('Unauthorized');

  const userId = (session.user as any).id;

  const message = await prisma.message.create({
    data: {
      content,
      senderId: userId,
      receiverId,
      chatId
    }
  });

  // Update chat updated timestamp
  await prisma.chat.update({
    where: { id: chatId },
    data: { updatedAt: new Date() }
  });

  revalidatePath(`/chats/${chatId}`);
  return message;
}

export async function getOrCreateChatAction(otherUserId: number) {
  const session = await getServerSession(authOptions);
  if (!session?.user) throw new Error('Unauthorized');

  const userId = (session.user as any).id;

  // Find existing chat between these two users
  const existingChat = await prisma.chat.findFirst({
    where: {
      AND: [
        { users: { some: { id: userId } } },
        { users: { some: { id: otherUserId } } }
      ]
    }
  });

  if (existingChat) return existingChat.id;

  // Create new chat
  const newChat = await prisma.chat.create({
    data: {
      users: {
        connect: [{ id: userId }, { id: otherUserId }]
      }
    }
  });

  return newChat.id;
}
