import { useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Badge, Button, Card, Field, fmtTime, inputCls } from '@/Components/UI';

export default function Create({ unit, now, today, rules, taken }) {
  const { data, setData, post, processing, errors } = useForm({ facility_unit_id: unit.id, start_time: now, end_time: '', purpose: '' });
  const submit = (e) => { e.preventDefault(); post(route('bookings.store')); };
  const f = unit.facility;
  return (
    <AppLayout title="Konfirmasi Booking">
      <div className="grid gap-4 lg:grid-cols-2">
        <Card>
          <h2 className="text-lg font-semibold">{unit.name || f.name} · {unit.code}</h2>
          <p className="text-sm text-slate-500">{[unit.floor && `Lantai ${unit.floor}`, unit.location || f.location].filter(Boolean).join(' · ')}</p>
          <div className="mt-2 flex flex-wrap gap-1">{f.amenities?.map((a) => <Badge key={a.id} tone="blue">{a.name}</Badge>)}</div>
          <h3 className="mt-4 text-sm font-semibold">Sudah dipesan hari ini</h3>
          {taken.length ? <ul className="mt-1 text-sm text-slate-600 dark:text-slate-300">{taken.map((t, i) => <li key={i}>{fmtTime(t.starts_at)}–{fmtTime(t.ends_at)}</li>)}</ul> : <p className="text-sm text-slate-500">Belum ada.</p>}
        </Card>
        <Card>
          <form onSubmit={submit} className="space-y-4">
            <p className="text-sm">Tanggal: <b>{today}</b> (booking hanya untuk hari ini)</p>
            <div className="grid grid-cols-2 gap-3">
              <Field label="Jam mulai" error={errors.start_time}><input type="time" className={inputCls} value={data.start_time} onChange={(e) => setData('start_time', e.target.value)} required /></Field>
              <Field label="Jam selesai" error={errors.end_time}><input type="time" className={inputCls} value={data.end_time} onChange={(e) => setData('end_time', e.target.value)} required /></Field>
            </div>
            <Field label="Tujuan (opsional)" error={errors.purpose}><input className={inputCls} value={data.purpose} onChange={(e) => setData('purpose', e.target.value)} maxLength={255} /></Field>
            {errors.facility_unit_id && <p className="text-sm text-rose-600">{errors.facility_unit_id}</p>}
            <ul className="text-xs text-slate-500">
              <li>Durasi: {rules.min || rules.max ? `min ${rules.min ?? '-'} / maks ${rules.max ?? '-'} menit` : 'belum dibatasi admin'}</li>
              <li>Wajib check-in QR paling lambat {rules.grace} menit setelah jam mulai.</li>
            </ul>
            <Button type="submit" disabled={processing} className="w-full">{processing ? 'Memproses…' : 'Konfirmasi booking'}</Button>
          </form>
        </Card>
      </div>
    </AppLayout>
  );
}
