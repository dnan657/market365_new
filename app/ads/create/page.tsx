'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { createAdAction } from './actions';
import { Camera, X, Loader2 } from 'lucide-react';
import PostcodePicker from '@/components/PostcodePicker';

export default function CreateAdPage() {
  const [images, setImages] = useState<string[]>([]);
  const [isUploading, setIsUploading] = useState(false);
  const router = useRouter();

  const handleImageUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (!files) return;

    setIsUploading(true);
    const newImages = [...images];

    for (let i = 0; i < files.length; i++) {
      const formData = new FormData();
      formData.append('file', files[i]);

      try {
        const res = await fetch('/api/upload', {
          method: 'POST',
          body: formData,
        });
        const data = await res.json();
        if (data.url) {
          newImages.push(data.url);
        }
      } catch (err) {
        console.error('Upload failed', err);
      }
    }

    setImages(newImages);
    setIsUploading(false);
  };

  const removeImage = (index: number) => {
    setImages(images.filter((_, i) => i !== index));
  };

  const locations = ["London", "Manchester", "Birmingham", "Leeds", "Glasgow", "Bristol", "Edinburgh"];
  const categories = ["Cars & Vehicles", "Property", "Electronics", "Home & Garden", "Pets", "Jobs"];

  return (
    <div className="max-w-3xl mx-auto">
      <h1 className="text-3xl font-bold mb-8">Post an Ad</h1>

      <form action={createAdAction} className="bg-white p-8 border rounded-2xl shadow-sm space-y-8">
        <div className="space-y-6">
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

          <PostcodePicker />

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

          <div className="space-y-4">
            <label className="block font-bold text-gray-700">Upload Photos</label>
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
              {images.map((url, index) => (
                <div key={index} className="relative aspect-square rounded-lg overflow-hidden border">
                  <img src={url} alt={`Upload ${index}`} className="w-full h-full object-cover" />
                  <button
                    type="button"
                    onClick={() => removeImage(index)}
                    className="absolute top-1 right-1 bg-black/60 text-white rounded-full p-1 hover:bg-black"
                  >
                    <X className="w-4 h-4" />
                  </button>
                </div>
              ))}
              {images.length < 8 && (
                <label className="flex flex-col items-center justify-center aspect-square border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                  {isUploading ? <Loader2 className="w-8 h-8 text-blue-600 animate-spin" /> : <Camera className="w-8 h-8 text-gray-400" />}
                  <span className="text-xs text-gray-500 mt-2 font-medium">Add Photo</span>
                  <input type="file" className="hidden" accept="image/*" multiple onChange={handleImageUpload} disabled={isUploading} />
                </label>
              )}
            </div>
            {images.map((url, index) => (
              <input key={index} type="hidden" name="imageUrls" value={url} />
            ))}
          </div>
        </div>

        <div className="pt-4">
          <button type="submit" className="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition-colors shadow-lg disabled:bg-gray-400" disabled={isUploading}>
            {isUploading ? 'Uploading images...' : 'Post My Ad Now'}
          </button>
          <p className="text-center text-sm text-gray-500 mt-4">
            By clicking "Post My Ad Now", you agree to our <button className="text-blue-600 hover:underline">Terms of Use</button> and <button className="text-blue-600 hover:underline">Privacy Policy</button>.
          </p>
        </div>
      </form>
    </div>
  );
}
