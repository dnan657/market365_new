'use client';

interface Field {
  id: number;
  name: string;
  type: string;
  options: string | null;
  required: boolean;
}

interface CategoryFieldsProps {
  fields: Field[];
}

export function CategoryFields({ fields }: CategoryFieldsProps) {
  if (!fields || fields.length === 0) return null;

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
      <div className="col-span-full border-b pb-4 mb-2">
        <h3 className="font-black text-gray-900 tracking-tight text-xl">Item Specifications</h3>
        <p className="text-gray-500 text-sm font-medium">Please provide specific details for your listing</p>
      </div>
      {fields.map((field) => (
        <div key={field.id} className="space-y-2">
          <label htmlFor={`field-${field.id}`} className="block text-xs font-black text-gray-500 uppercase tracking-widest">
            {field.name} {field.required && <span className="text-red-500">*</span>}
          </label>

          {field.type === 'SELECT' ? (
            <select
              id={`field-${field.id}`}
              name={`attr-${field.id}`}
              required={field.required}
              className="w-full border-2 border-gray-100 rounded-2xl p-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all bg-white font-medium"
            >
              <option value="">Select {field.name}</option>
              {JSON.parse(field.options || '[]').map((opt: string) => (
                <option key={opt} value={opt}>{opt}</option>
              ))}
            </select>
          ) : (
            <input
              id={`field-${field.id}`}
              name={`attr-${field.id}`}
              type={field.type === 'NUMBER' ? 'number' : 'text'}
              required={field.required}
              placeholder={`Enter ${field.name.toLowerCase()}`}
              className="w-full border-2 border-gray-100 rounded-2xl p-4 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 transition-all bg-white font-medium"
            />
          )}
        </div>
      ))}
    </div>
  );
}
