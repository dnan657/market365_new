import prisma from '@/lib/prisma';
import { getServerSession } from 'next-auth';
import { authOptions } from '@/lib/auth-options';
import Link from 'next/link';
import { MessageSquare } from 'lucide-react';
import { redirect } from 'next/navigation';

export default async function ChatsPage() {
  const session = await getServerSession(authOptions);
  if (!session?.user) redirect('/login');

  const userId = (session.user as any).id;

  const chats = await prisma.chat.findMany({
    where: {
      users: { some: { id: userId } }
    },
    include: {
      users: true,
      messages: {
        orderBy: { createdAt: 'desc' },
        take: 1
      }
    },
    orderBy: { updatedAt: 'desc' }
  });

  return (
    <div className="max-w-4xl mx-auto space-y-8">
      <h1 className="text-4xl font-black text-gray-900 tracking-tight">Your Messages</h1>

      <div className="bg-white rounded-3xl border shadow-xl shadow-gray-100 overflow-hidden">
        {chats.length > 0 ? (
          <div className="divide-y">
            {chats.map((chat) => {
              const otherUser = chat.users.find(u => u.id !== userId);
              const lastMessage = chat.messages[0];

              return (
                <Link
                  key={chat.id}
                  href={`/chats/${chat.id}`}
                  className="flex items-center gap-4 p-6 hover:bg-gray-50 transition-colors"
                >
                  <div className="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-black text-xl">
                    {otherUser?.name?.[0] || 'U'}
                  </div>
                  <div className="flex-1">
                    <div className="flex justify-between items-baseline mb-1">
                      <h3 className="font-bold text-gray-900">{otherUser?.name}</h3>
                      {lastMessage && (
                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                          {new Date(lastMessage.createdAt).toLocaleDateString('en-GB')}
                        </span>
                      )}
                    </div>
                    <p className="text-sm text-gray-500 line-clamp-1 font-medium">
                      {lastMessage?.content || 'Start a conversation...'}
                    </p>
                  </div>
                </Link>
              );
            })}
          </div>
        ) : (
          <div className="py-20 text-center text-gray-400">
            <MessageSquare className="w-12 h-12 mx-auto mb-4 opacity-20" />
            <p className="text-lg font-bold italic">No messages yet.</p>
          </div>
        )}
      </div>
    </div>
  );
}
