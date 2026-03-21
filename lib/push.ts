'use client';

import { useEffect } from 'react';

export function PushNotificationManager() {
  useEffect(() => {
    if (typeof window !== 'undefined' && 'Notification' in window) {
      if (Notification.permission === 'default') {
        Notification.requestPermission();
      }
    }
  }, []);

  // This component doesn't render anything, just manages permission
  return null;
}

export function sendLocalNotification(title: string, body: string) {
  if (typeof window !== 'undefined' && 'Notification' in window && Notification.permission === 'granted') {
    new Notification(title, { body, icon: '/favicon.ico' });
  }
}
