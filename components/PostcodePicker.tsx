'use client';

import { MapPin, Info } from 'lucide-react';

export default function PostcodePicker() {
  return (
    <div className="space-y-4 bg-blue-50/50 p-6 rounded-2xl border border-blue-100">
      <div className="flex items-center gap-2 mb-2">
        <MapPin className="w-5 h-5 text-blue-600" />
        <h2 className="font-bold text-gray-900">Precise Location</h2>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div className="space-y-1">
          <label htmlFor="postcode" className="text-xs font-bold text-gray-500 uppercase">UK Postcode (e.g. SW1A 1AA)</label>
          <input
            id="postcode"
            name="postcode"
            type="text"
            placeholder="SW1A"
            className="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 uppercase"
            onChange={(e) => {
              e.target.value = e.target.value.toUpperCase();
            }}
          />
        </div>
        <div className="space-y-1">
          <label className="text-xs font-bold text-gray-500 uppercase">Coordinates (Auto-detected)</label>
          <div className="flex gap-2">
            <input name="lat" type="hidden" value="51.5074" />
            <input name="lng" type="hidden" value="-0.1278" />
            <div className="w-full bg-gray-100 border rounded-lg p-2.5 text-xs text-gray-400 font-mono">
              Lat: 51.5074, Lng: -0.1278
            </div>
          </div>
        </div>
      </div>

      <div className="flex items-start gap-2 text-xs text-blue-700 mt-2">
        <Info className="w-4 h-4 shrink-0" />
        <p>Using a postcode helps local buyers find your items more easily. We only show the general area to protect your privacy.</p>
      </div>
    </div>
  );
}
