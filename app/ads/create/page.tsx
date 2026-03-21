'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { createAdAction } from './actions';
import { Camera, X, Loader2 } from 'lucide-react';
import PostcodePicker from '@/components/PostcodePicker';
import { CategoryFields } from '@/components/CategoryFields';

export default function CreateAdPage() {
  const [images, setImages] = useState<string[]>([]);
  const [isUploading, setIsUploading] = useState(false);
  const [categories, setCategories] = useState<any[]>([]);
  const [selectedSub, setSelectedSub] = useState<any>(null);
  const router = useRouter();

  useEffect(() => {
    // Fetch categories and subcategories
    const fetchCats = async () => {
      const res = await fetch('/api/categories');
      const data = await res.json();
      setCategories(data);
    };
    fetchCats();
  }, []);

  const handleImageUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (!files) return;
    setIsUploading(true);
    const newImages = [...images];
    for (let i = 0; i < files.length; i++) {
      const formData = new FormData();
      formData.append('file', files[i]);
      try {
        const res = await fetch('/api/upload', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.url) newImages.push(data.url);
      } catch (err) { console.error(err); }
    }
    setImages(newImages);
    setIsUploading(false);
  };

  const removeImage = (index: number) => setImages(images.filter((_, i) => i !== index));

  const handleSubChange = (id: string) => {
    const sub = categories.flatMap(c => c.subcategories).find(s => s.id === parseInt(id));
    setSelectedSub(sub);
  };

  return (
    <div className="max-w-4xl mx-auto py-10">
      <h1 className="text-5xl font-black mb-12 tracking-tight">Post your Ad</h1>

      <form action={createAdAction} className="bg-white p-10 rounded-[3rem] border shadow-xl shadow-gray-100 space-y-12">
        <div className="space-y-8">
          {/* Title */}
          <div className="space-y-3">
            <label htmlFor="title" className="block text-xs font-black text-gray-400 uppercase tracking-widest">What are you selling?</label>
            <input
              id="title" name="title" type="text" required placeholder="e.g. 2018 Volkswagen Golf, Full Service History"
              className="w-full border-2 border-gray-100 rounded-2xl p-5 text-xl font-bold outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all"
            />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {/* Subcategory */}
            <div className="space-y-3">
              <label htmlFor="subcategory" className="block text-xs font-black text-gray-400 uppercase tracking-widest">Category</label>
              <select
                id="subcategory" name="subcategory" required
                onChange={(e) => handleSubChange(e.target.value)}
                className="w-full border-2 border-gray-100 rounded-2xl p-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all bg-white font-bold"
              >
                <option value="">Select a Category</option>
                {categories.map(cat => (
                  <optgroup key={cat.id} label={cat.name}>
                    {cat.subcategories.map((sub: any) => (
                      <option key={sub.id} value={sub.id}>{sub.name}</option>
                    ))}
                  </optgroup>
                ))}
              </select>
            </div>

            {/* Price */}
            <div className="space-y-3">
              <label htmlFor="price" className="block text-xs font-black text-gray-400 uppercase tracking-widest">Price (£)</label>
              <input
                id="price" name="price" type="number" required placeholder="0.00"
                className="w-full border-2 border-gray-100 rounded-2xl p-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all font-bold"
              />
            </div>
          </div>

          {/* Dynamic Fields */}
          {selectedSub && <CategoryFields fields={selectedSub.fields} />}

          {/* Location */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
             <div className="space-y-3">
                <label htmlFor="location" className="block text-xs font-black text-gray-400 uppercase tracking-widest">UK Region</label>
                <select id="location" name="location" required className="w-full border-2 border-gray-100 rounded-2xl p-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all bg-white font-bold">
                  {["London", "Manchester", "Birmingham", "Leeds", "Glasgow", "Bristol", "Edinburgh"].map(loc => <option key={loc} value={loc}>{loc}</option>)}
                </select>
             </div>
             <PostcodePicker />
          </div>

          {/* Description */}
          <div className="space-y-3">
            <label htmlFor="description" className="block text-xs font-black text-gray-400 uppercase tracking-widest">Description</label>
            <textarea
              id="description" name="description" rows={8} required
              placeholder="Provide a detailed description of your item..."
              className="w-full border-2 border-gray-100 rounded-2xl p-5 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all font-medium leading-relaxed"
            ></textarea>
          </div>

          {/* Photos */}
          <div className="space-y-4">
            <label className="block text-xs font-black text-gray-400 uppercase tracking-widest">Add Photos (Max 8)</label>
            <div className="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-4">
              {images.map((url, index) => (
                <div key={index} className="relative aspect-square rounded-2xl overflow-hidden border shadow-sm group">
                  <img src={url} alt="Ad Image" className="w-full h-full object-cover transition-transform group-hover:scale-110" />
                  <button type="button" onClick={() => removeImage(index)} className="absolute top-2 right-2 bg-black/60 text-white rounded-full p-1.5 hover:bg-black backdrop-blur-sm">
                    <X className="w-4 h-4" />
                  </button>
                </div>
              ))}
              {images.length < 8 && (
                <label className="flex flex-col items-center justify-center aspect-square border-4 border-dashed border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-blue-200 transition-all text-gray-300 hover:text-blue-500">
                  {isUploading ? <Loader2 className="w-10 h-10 animate-spin" /> : <Camera className="w-10 h-10" />}
                  <span className="text-[10px] font-black uppercase tracking-widest mt-2">Upload</span>
                  <input type="file" className="hidden" accept="image/*" multiple onChange={handleImageUpload} disabled={isUploading} />
                </label>
              )}
            </div>
            {images.map((url, index) => <input key={index} type="hidden" name="imageUrls" value={url} />)}
          </div>
        </div>

        <div className="pt-10 border-t">
          <button type="submit" className="w-full bg-blue-600 text-white py-6 rounded-[2rem] font-black text-2xl hover:bg-blue-700 transition-all hover:scale-[1.02] active:scale-[0.98] shadow-2xl shadow-blue-100 disabled:bg-gray-300" disabled={isUploading}>
            {isUploading ? 'Preparing your listing...' : 'Post Listing Now'}
          </button>
          <p className="text-center text-xs text-gray-400 font-bold uppercase tracking-widest mt-6">
            By posting, you agree to the UK Classifieds <button className="text-blue-600 underline">Terms of Service</button>
          </p>
        </div>
      </form>
    </div>
  );
}
