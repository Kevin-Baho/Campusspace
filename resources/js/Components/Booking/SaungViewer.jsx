import { useState } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

/** Tampilan satu arah: maju/mundur antar sudut yang disediakan (view_images). Bukan 360°. */
export default function SaungViewer({ unit, onClose }) {
  const imgs = unit.view_images || [];
  const [i, setI] = useState(0);
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" onClick={onClose}>
      <div className="w-full max-w-2xl rounded-2xl bg-white p-4 dark:bg-slate-800" onClick={(e) => e.stopPropagation()}>
        <div className="mb-3 flex items-center justify-between"><h3 className="font-semibold">{unit.name || unit.code}</h3><button onClick={onClose}>Tutup</button></div>
        {imgs.length === 0 ? <p className="py-10 text-center text-sm text-slate-500">Belum ada foto lokasi untuk unit ini.</p> : (
          <div className="relative">
            <img src={imgs[i]} alt={`Sudut ${i + 1}`} className="h-72 w-full rounded-xl object-cover" />
            <button disabled={i === 0} onClick={() => setI(i - 1)} className="absolute left-2 top-1/2 rounded-full bg-white/90 p-2 disabled:opacity-40" aria-label="Mundur"><ChevronLeft /></button>
            <button disabled={i === imgs.length - 1} onClick={() => setI(i + 1)} className="absolute right-2 top-1/2 rounded-full bg-white/90 p-2 disabled:opacity-40" aria-label="Maju"><ChevronRight /></button>
            <div className="mt-2 text-center text-xs text-slate-500">Sudut {i + 1} dari {imgs.length}</div>
          </div>
        )}
      </div>
    </div>
  );
}
