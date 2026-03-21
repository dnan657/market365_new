import { Resend } from 'resend';

const resend = new Resend(process.env.RESEND_API_KEY);

export async function sendEmail({ to, subject, html }: { to: string; subject: string; html: string }) {
  try {
    const data = await resend.emails.send({
      from: 'UK Classifieds <noreply@uk-classifieds.co.uk>',
      to,
      subject,
      html,
    });
    return data;
  } catch (error) {
    console.error('Email sending failed', error);
    return null;
  }
}

export async function sendVerificationEmail(email: string, url: string) {
  return sendEmail({
    to: email,
    subject: 'Sign in to UK Classifieds',
    html: `
      <div style="font-family: sans-serif; max-width: 600px; margin: auto; padding: 40px; border: 1px solid #eee; border-radius: 10px;">
        <h1 style="color: #2563eb;">UK Classifieds</h1>
        <p style="font-size: 16px; color: #374151;">Click the button below to sign in to your account. This link will expire in 24 hours.</p>
        <a href="${url}" style="display: inline-block; background-color: #2563eb; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 20px;">Sign In Now</a>
        <p style="font-size: 12px; color: #9ca3af; margin-top: 40px;">If you didn't request this email, you can safely ignore it.</p>
      </div>
    `
  });
}
