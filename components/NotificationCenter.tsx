'use client';

import { useState, useEffect } from 'react';
import { Bell, X, Info, MessageSquare, Zap } from 'lucide-react';
import { useSession } from 'next-auth/react';

export function NotificationCenter() {
  const { data: session } = useSession();
  const [isOpen, setIsOpen] = useState(false);
  const [notifications, setNotifications] = useState<any[]>([]);

  useEffect(() => {
    if (session) {
      // Simulate fetching notifications
      setNotifications([
        { id: 1, title: 'Welcome to UK Classifieds!', message: 'Thanks for joining the UK\'s favourite marketplace.', type: 'SYSTEM', read: false },
        { id: 2, title: 'New Message', message: 'You have a new message regarding your ad.', type: 'MESSAGE', read: false },
      ]);
    }
  }, [session]);

  const unreadCount = notifications.filter(n => !n.read).length;

  return (
    <div className="relative">
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="relative p-2 text-gray-500 hover:text-blue-600 transition-colors"
      >
        <Bell className="w-6 h-6" />
        {unreadCount > 0 && (
          <span className="absolute top-1.5 right-1.5 w-4 h-4 bg-red-600 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white">
            {unreadCount}
          </span>
        )}
      </button>

      {isOpen && (
        <>
          <div className="fixed inset-0 z-40" onClick={() => setIsOpen(false)} />
          <div className="absolute right-0 mt-2 w-80 bg-white rounded-3xl border shadow-2xl z-50 overflow-hidden">
            <div className="p-6 border-b bg-gray-50/50 flex items-center justify-between">
              <h3 className="font-black text-gray-900">Notifications</h3>
              <button onClick={() => setIsOpen(false)}><X className="w-4 h-4 text-gray-400" /></button>
            </div>
            <div className="max-h-96 overflow-y-auto">
              {notifications.length > 0 ? (
                <div className="divide-y">
                  {notifications.map(n => (
                    <div key={n.id} className="p-4 hover:bg-gray-50 transition-colors flex gap-4">
                      <div className={`p-2 rounded-xl shrink-0 h-fit ${n.type === 'MESSAGE' ? 'bg-blue-100 text-blue-600' : (n.type === 'PROMOTION' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-600')}`}>
                        {n.type === 'MESSAGE' ? <MessageSquare className="w-4 h-4" /> : (n.type === 'PROMOTION' ? <Zap className="w-4 h-4" /> : <Info className="w-4 h-4" />)}
                      </div>
                      <div className="space-y-1">
                        <p className="text-sm font-bold text-gray-900 leading-tight">{n.title}</p>
                        <p className="text-xs text-gray-500 font-medium leading-relaxed">{n.message}</p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="p-10 text-center text-gray-400">
                  <Bell className="w-8 h-8 mx-auto mb-2 opacity-20" />
                  <p className="text-sm font-bold italic">No new notifications</p>
                </div>
              )}
            </div>
            <div className="p-4 bg-gray-50 border-t text-center">
               <button className="text-xs font-black text-blue-600 hover:underline uppercase tracking-widest">Mark all as read</button>
            </div>
          </div>
        </>
      )}
    </div>
  );
}
