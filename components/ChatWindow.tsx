'use client';

import { useState, useEffect, useRef } from 'react';
import { Send, MapPin } from 'lucide-react';
import { sendMessageAction } from '@/app/chats/actions';

interface ChatWindowProps {
  chatId: string;
  initialMessages: any[];
  userId: number;
  otherUser: any;
}

export default function ChatWindow({ chatId, initialMessages, userId, otherUser }: ChatWindowProps) {
  const [messages, setMessages] = useState(initialMessages);
  const [content, setContent] = useState('');
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    scrollRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  const handleSend = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!content.trim()) return;

    const optimisticMessage = {
      id: Math.random().toString(),
      content,
      senderId: userId,
      createdAt: new Date(),
    };

    setMessages([...messages, optimisticMessage]);
    setContent('');

    try {
      await sendMessageAction(chatId, content, otherUser.id);
    } catch (err) {
      console.error('Send failed', err);
    }
  };

  return (
    <div className="flex flex-col h-[70vh] bg-white rounded-3xl border shadow-2xl overflow-hidden">
      {/* Header */}
      <div className="p-6 border-b flex items-center gap-4 bg-gray-50/50">
        <div className="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-lg text-white">
          {otherUser?.name?.[0] || 'U'}
        </div>
        <div>
          <h2 className="font-black text-gray-900">{otherUser?.name}</h2>
          <p className="text-[10px] text-green-500 font-bold uppercase tracking-widest flex items-center gap-1">
            <span className="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse" />
            Online
          </p>
        </div>
      </div>

      {/* Messages */}
      <div className="flex-1 overflow-y-auto p-6 space-y-4">
        {messages.map((m) => (
          <div
            key={m.id}
            className={`flex ${m.senderId === userId ? 'justify-end' : 'justify-start'}`}
          >
            <div className={`max-w-[80%] p-4 rounded-2xl text-sm font-medium ${
              m.senderId === userId
                ? 'bg-blue-600 text-white rounded-tr-none shadow-lg shadow-blue-100'
                : 'bg-gray-100 text-gray-800 rounded-tl-none border border-gray-200'
            }`}>
              {m.content}
              <p className={`text-[9px] mt-1 font-bold ${m.senderId === userId ? 'text-blue-100' : 'text-gray-400'}`}>
                {new Date(m.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
              </p>
            </div>
          </div>
        ))}
        <div ref={scrollRef} />
      </div>

      {/* Input */}
      <form onSubmit={handleSend} className="p-6 border-t bg-gray-50/30 flex gap-4">
        <input
          value={content}
          onChange={(e) => setContent(e.target.value)}
          placeholder="Type your message..."
          className="flex-1 border-2 border-gray-100 rounded-2xl px-6 py-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all font-medium"
        />
        <button className="bg-blue-600 text-white p-4 rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
          <Send className="w-6 h-6" />
        </button>
      </form>
    </div>
  );
}
