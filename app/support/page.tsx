'use client';

import { useState } from 'react';
import { Mail, MessageSquare, Send, CheckCircle2 } from 'lucide-react';
import { createTicketAction } from './actions';

export default function SupportPage() {
  const [submitted, setSubmitted] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setIsSubmitting(true);
    const formData = new FormData(e.currentTarget);
    try {
      await createTicketAction(formData);
      setSubmitted(true);
    } catch (err) {
      console.error('Failed to submit ticket', err);
    } finally {
      setIsSubmitting(false);
    }
  };

  if (submitted) {
    return (
      <div className="max-w-xl mx-auto py-20 text-center space-y-6">
        <div className="inline-block p-4 bg-green-100 rounded-full text-green-600 mb-4 scale-150">
          <CheckCircle2 className="w-12 h-12" />
        </div>
        <h1 className="text-4xl font-black text-gray-900">Ticket Submitted!</h1>
        <p className="text-xl text-gray-500 leading-relaxed">
          Thanks for contacting UK Classifieds support. Our team will get back to you within 24 hours.
        </p>
        <button
          onClick={() => setSubmitted(false)}
          className="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-colors"
        >
          Go Back
        </button>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto space-y-12">
      <div className="text-center space-y-4">
        <h1 className="text-5xl font-black text-gray-900">How can we help?</h1>
        <p className="text-xl text-gray-500">Search our help center or contact a member of our support team.</p>
      </div>

      <div className="grid md:grid-cols-2 gap-8">
        <div className="bg-white p-10 rounded-3xl border shadow-sm space-y-8">
          <div>
            <h2 className="text-2xl font-black text-gray-900 mb-2">Send us a message</h2>
            <p className="text-gray-500 text-sm">Fill out the form below and we'll start a support ticket for you.</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="text-xs font-bold text-gray-500 uppercase">Your Name</label>
              <input name="name" type="text" required placeholder="Full Name" className="w-full border rounded-2xl p-4 outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div className="space-y-2">
              <label className="text-xs font-bold text-gray-500 uppercase">Email Address</label>
              <input name="email" type="email" required placeholder="name@example.co.uk" className="w-full border rounded-2xl p-4 outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div className="space-y-2">
              <label className="text-xs font-bold text-gray-500 uppercase">Subject</label>
              <input name="subject" type="text" required placeholder="What do you need help with?" className="w-full border rounded-2xl p-4 outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div className="space-y-2">
              <label className="text-xs font-bold text-gray-500 uppercase">Message</label>
              <textarea name="message" rows={5} required placeholder="Describe your issue in detail..." className="w-full border rounded-2xl p-4 outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <button disabled={isSubmitting} className="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-lg flex items-center justify-center gap-2 hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 disabled:bg-gray-400">
              <Send className="w-5 h-5" /> {isSubmitting ? 'Submitting...' : 'Submit Ticket'}
            </button>
          </form>
        </div>

        <div className="space-y-8">
          <div className="bg-blue-600 p-10 rounded-3xl text-white space-y-6 shadow-xl shadow-blue-200">
            <h2 className="text-2xl font-black">Helpful Guides</h2>
            <ul className="space-y-4">
              <li className="flex items-center gap-3 border-b border-blue-500 pb-4">
                <div className="w-8 h-8 bg-blue-500/50 rounded-lg flex items-center justify-center font-bold">1</div>
                <button className="font-bold hover:underline">How to post a successful ad</button>
              </li>
              <li className="flex items-center gap-3 border-b border-blue-500 pb-4">
                <div className="w-8 h-8 bg-blue-500/50 rounded-lg flex items-center justify-center font-bold">2</div>
                <button className="font-bold hover:underline">Staying safe on UK Classifieds</button>
              </li>
              <li className="flex items-center gap-3">
                <div className="w-8 h-8 bg-blue-500/50 rounded-lg flex items-center justify-center font-bold">3</div>
                <button className="font-bold hover:underline">Promoting your listings</button>
              </li>
            </ul>
          </div>

          <div className="bg-white p-10 rounded-3xl border shadow-sm space-y-6">
            <h2 className="text-2xl font-black text-gray-900">Other ways to reach us</h2>
            <div className="space-y-4">
              <div className="flex items-center gap-4 p-4 border rounded-2xl hover:bg-gray-50 transition-colors cursor-pointer">
                <div className="p-3 bg-blue-100 text-blue-600 rounded-xl">
                  <Mail className="w-6 h-6" />
                </div>
                <div>
                  <p className="font-black">Email Support</p>
                  <p className="text-xs text-gray-500">support@uk-classifieds.co.uk</p>
                </div>
              </div>
              <div className="flex items-center gap-4 p-4 border rounded-2xl hover:bg-gray-50 transition-colors cursor-pointer">
                <div className="p-3 bg-purple-100 text-purple-600 rounded-xl">
                  <MessageSquare className="w-6 h-6" />
                </div>
                <div>
                  <p className="font-black">Live Chat</p>
                  <p className="text-xs text-gray-500">Mon-Fri, 9am - 6pm</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
