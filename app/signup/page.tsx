import Link from 'next/link';

export default function SignupPage() {
  return (
    <div className="max-w-md mx-auto py-20">
      <div className="bg-white p-8 border rounded-2xl shadow-sm space-y-6">
        <h1 className="text-2xl font-bold text-center">Join UK Classifieds</h1>
        <p className="text-gray-500 text-center text-sm -mt-4">Start buying and selling locally today</p>

        <form className="space-y-4">
          <div className="space-y-2 text-sm">
            <label className="block font-bold text-gray-700">Full Name</label>
            <input
              type="text"
              required
              placeholder="e.g. John Smith"
              className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div className="space-y-2 text-sm">
            <label className="block font-bold text-gray-700">Email Address</label>
            <input
              type="email"
              required
              placeholder="name@example.co.uk"
              className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div className="space-y-2 text-sm">
            <label className="block font-bold text-gray-700">Password</label>
            <input
              type="password"
              required
              placeholder="At least 8 characters"
              className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <p className="text-xs text-gray-500 py-2 leading-relaxed">
            By creating an account, you agree to our <button className="text-blue-600 hover:underline">Terms of Use</button> and <button className="text-blue-600 hover:underline">Privacy Policy</button>. We'll occasionally send you account-related emails.
          </p>
          <button className="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors">
            Create Account
          </button>
        </form>

        <p className="text-center text-sm text-gray-600 pt-4">
          Already have an account? <Link href="/login" className="text-blue-600 font-bold hover:underline">Log In</Link>
        </p>
      </div>
    </div>
  );
}
