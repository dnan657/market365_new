import prisma from '@/lib/prisma';
import { MessageCircle, Clock, CheckCircle, User } from 'lucide-react';
import { revalidatePath } from 'next/cache';
import { isAdminAction } from '@/lib/auth';

export default async function AdminTicketsPage() {
  const tickets = await prisma.ticket.findMany({
    include: { user: true },
    orderBy: { createdAt: 'desc' }
  });

  async function closeTicket(id: number) {
    'use server';
    await isAdminAction();
    await prisma.ticket.update({
      where: { id },
      data: { status: 'CLOSED' }
    });
    revalidatePath('/admin/tickets');
  }

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold">Support Tickets</h1>
        <p className="text-gray-500">Manage user inquiries and technical issues.</p>
      </div>

      <div className="grid gap-6">
        {tickets.length > 0 ? (
          tickets.map((ticket) => (
            <div key={ticket.id} className="bg-white p-6 rounded-2xl border shadow-sm flex flex-col md:flex-row gap-6">
              <div className="flex-1 space-y-4">
                <div className="flex items-center gap-3">
                  <span className={`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${ticket.status === 'OPEN' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'}`}>
                    {ticket.status}
                  </span>
                  <p className="text-xs text-gray-400 font-medium">
                    Ticket #{ticket.id} • {new Date(ticket.createdAt).toLocaleString('en-GB')}
                  </p>
                </div>
                <div>
                  <h3 className="text-xl font-bold text-gray-900">{ticket.subject}</h3>
                  <p className="text-gray-600 mt-2 leading-relaxed">{ticket.message}</p>
                </div>
                <div className="flex items-center gap-2 pt-4 border-t border-gray-50">
                  <div className="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-xs font-bold text-gray-500">
                    {ticket.user.name?.[0] || 'U'}
                  </div>
                  <div>
                    <p className="text-sm font-bold">{ticket.user.name}</p>
                    <p className="text-xs text-gray-400">{ticket.user.email}</p>
                  </div>
                </div>
              </div>
              <div className="md:w-48 flex items-start justify-end gap-2">
                {ticket.status === 'OPEN' && (
                  <form action={closeTicket.bind(null, ticket.id)}>
                    <button className="w-full bg-green-50 text-green-600 px-4 py-2 rounded-xl text-sm font-bold hover:bg-green-100 transition-colors flex items-center gap-2">
                      <CheckCircle className="w-4 h-4" />
                      Resolve
                    </button>
                  </form>
                )}
              </div>
            </div>
          ))
        ) : (
          <div className="py-20 text-center text-gray-400 bg-white border rounded-2xl">
            <MessageCircle className="w-12 h-12 mx-auto mb-4 opacity-20" />
            <p className="text-lg font-bold">No tickets found.</p>
          </div>
        )}
      </div>
    </div>
  );
}
