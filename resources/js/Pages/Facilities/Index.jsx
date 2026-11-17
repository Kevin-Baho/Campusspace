import { router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import UnitCard from '@/Components/Cards/UnitCard';
import SaungViewer from '@/Components/Booking/SaungViewer';
import { Button, Empty, Field, inputCls } from '@/Components/UI';

const titles = { library: 'Library Booking', saung: 'Saung Booking', room: 'Room Booking' };

export default function Index({ category, units, ratings, amenities, filters }) {
  const [f, setF] = useState({ start_time: filters.start_time || '', end_time: filters.end_time || '', min_capacity: filters.min_capacity || '', amenity: filters.amenity || '' });
  const [view, setView] = useState(null);
  const apply = (e) => { e.preventDefault(); router.get(route('facilities.index', category), Object.fromEntries(Object.entries(f).filter(([, v]) => v)), { preserveState: true }); };
  const set = (k) => (e) => setF({ ...f, [k]: e.target.value });

  return (
    <AppLayout title={titles[category]}>
      <form onSubmit={apply} className="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800 sm:grid-cols-2 lg:grid-cols-5">
        <Field label="Cek mulai"><input type="time" className={inputCls} value={f.start_time} onChange={set('start_time')} /></Field>
        <Field label="Cek selesai"><input type="time" className={inputCls} value={f.end_time} onChange={set('end_time')} /></Field>
        {category !== 'library' && <Field label="Kapasitas min."><input type="number" min="1" className={inputCls} value={f.min_capacity} onChange={set('min_capacity')} /></Field>}
        {category !== 'library' && <Field label="Fasilitas"><select className={inputCls} value={f.amenity} onChange={set('amenity')}><option value="">Semua</option>{amenities.map((a) => <option key={a}>{a}</option>)}</select></Field>}
        <div className="flex items-end gap-2"><Button type="submit">Terapkan</Button><Button type="button" variant="ghost" onClick={() => router.get(route('facilities.index', category))}>Reset</Button></div>
      </form>

      {units.length === 0 ? <Empty>Tidak ada unit yang cocok dengan filter.</Empty> : (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {units.map((u) => <UnitCard key={u.id} unit={u} rating={ratings[u.facility_id]} onView={category === 'saung' ? setView : null} />)}
        </div>
      )}
      {view && <SaungViewer unit={view} onClose={() => setView(null)} />}
    </AppLayout>
  );
}
