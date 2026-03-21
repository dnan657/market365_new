import Link from 'next/link';

export default function LoginPage() {
  return (
    <div className="max-w-md mx-auto py-20">
      <div className="bg-white p-8 border rounded-2xl shadow-sm space-y-6">
        <h1 className="text-2xl font-bold text-center">Welcome back</h1>
        <p className="text-gray-500 text-center text-sm -mt-4">Sign in to manage your UK ads</p>

        <form className="space-y-4">
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
            <div className="flex justify-between">
              <label className="block font-bold text-gray-700">Password</label>
              <button type="button" className="text-blue-600 font-semibold hover:underline">Forgot?</button>
            </div>
            <input
              type="password"
              required
              className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <button className="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors">
            Sign In
          </button>
        </form>

        <div className="relative text-center py-4">
          <div className="absolute inset-0 flex items-center">
            <div className="w-full border-t border-gray-200"></div>
          </div>
          <span className="relative bg-white px-4 text-sm text-gray-400">or continue with</span>
        </div>

        <div className="grid grid-cols-2 gap-4">
          <button className="border py-2 px-4 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors text-sm">
            Google
          </button>
          <button className="border py-2 px-4 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors text-sm">
            Facebook
          </button>
        </div>

        <p className="text-center text-sm text-gray-600 pt-4">
          Don't have an account? <Link href="/signup" className="text-blue-600 font-bold hover:underline">Join now</Link>
        </p>
      </div>
    </div>
  );
}
