import prisma from '@/lib/prisma';
import { getServerSession } from 'next-auth';
import { authOptions } from '@/lib/auth-options';
import { notFound, redirect } from 'next/navigation';
import ChatWindow from '@/components/ChatWindow';

export default async function ChatPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const session = await getServerSession(authOptions);
  if (!session?.user) redirect('/login');

  const userId = (session.user as any).id;

  const chat = await prisma.chat.findUnique({
    where: { id },
    include: {
      users: true,
      messages: {
        orderBy: { createdAt: 'asc' }
      }
    }
  });

  if (!chat || !chat.users.some(u => u.id === userId)) {
    notFound();
  }

  const otherUser = chat.users.find(u => u.id !== userId);

  return (
    <div className="max-w-4xl mx-auto">
      <ChatWindow
        chatId={chat.id}
        initialMessages={chat.messages}
        userId={userId}
        otherUser={otherUser}
      />
    </div>
  );
}
