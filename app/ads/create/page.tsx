import { redirect } from 'next/navigation';
import prisma from '@/lib/prisma';

export default async function CreateAdPage() {
  async function createAd(formData: FormData) {
    'use server';

    const title = formData.get('title') as string;
    const category = formData.get('category') as string;
    const price = parseFloat(formData.get('price') as string);
    const location = formData.get('location') as string;
    const description = formData.get('description') as string;
    const imageUrl = formData.get('imageUrl') as string;

    // Hardcoded author for demo purposes since auth is just UI for now
    let user = await prisma.user.findFirst();
    if (!user) {
      user = await prisma.user.create({
        data: {
          email: 'demo@uk-classifieds.co.uk',
          name: 'Demo User',
          password: 'password123',
        }
      });
    }

    const ad = await prisma.ad.create({
      data: {
        title,
        category,
        price,
        location,
        description,
        imageUrl,
        authorId: user.id,
      },
    });

    redirect(`/ads/${ad.id}`);
  }

  const locations = ["London", "Manchester", "Birmingham", "Leeds", "Glasgow", "Bristol", "Edinburgh"];
  const categories = ["Cars & Vehicles", "Property", "Electronics", "Home & Garden", "Pets", "Jobs"];

  return (
    <div className="max-w-3xl mx-auto">
      <h1 className="text-3xl font-bold mb-8">Post an Ad</h1>

      <form action={createAd} className="bg-white p-8 border rounded-2xl shadow-sm space-y-6">
        <div className="space-y-2">
          <label htmlFor="title" className="block font-bold text-gray-700">Ad Title</label>
          <input
            id="title"
            name="title"
            type="text"
            required
            placeholder="e.g. 2018 Ford Fiesta, Low Mileage"
            className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-2">
            <label htmlFor="category" className="block font-bold text-gray-700">Category</label>
            <select id="category" name="category" required className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
              {categories.map(cat => <option key={cat} value={cat}>{cat}</option>)}
            </select>
          </div>
          <div className="space-y-2">
            <label htmlFor="price" className="block font-bold text-gray-700">Price (£)</label>
            <input
              id="price"
              name="price"
              type="number"
              required
              placeholder="0.00"
              className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>

        <div className="space-y-2">
          <label htmlFor="location" className="block font-bold text-gray-700">Location (UK City)</label>
          <select id="location" name="location" required className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            {locations.map(loc => <option key={loc} value={loc}>{loc}</option>)}
          </select>
        </div>

        <div className="space-y-2">
          <label htmlFor="description" className="block font-bold text-gray-700">Description</label>
          <textarea
            id="description"
            name="description"
            rows={6}
            required
            placeholder="Describe what you are selling. Include key features, condition, etc."
            className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
          ></textarea>
        </div>

        <div className="space-y-2">
          <label htmlFor="imageUrl" className="block font-bold text-gray-700">Image URL (Optional)</label>
          <input
            id="imageUrl"
            name="imageUrl"
            type="url"
            placeholder="https://example.com/image.jpg"
            className="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div className="pt-4">
          <button type="submit" className="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition-colors shadow-lg">
            Post My Ad Now
          </button>
          <p className="text-center text-xs text-gray-500 mt-4">
            By clicking "Post My Ad Now", you agree to our Terms of Use and Privacy Policy.
          </p>
        </div>
      </form>
    </div>
  );
}
