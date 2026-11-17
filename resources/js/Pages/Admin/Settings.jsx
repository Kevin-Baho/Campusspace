import { useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Button, Card, Field, inputCls } from '@/Components/UI';

export default function Settings({ settings }) {
  const { data, setData, patch, processing, errors } = useForm({ ...settings });
  const num = (k, label, hint) => (
    <Field label={label} error={errors[k]}><input type="number" min="0" className={inputCls} value={data[k] ?? ''} onChange={(e) => setData(k, e.target.value)} />{hint && <span className="text-xs text-slate-500">{hint}</span>}</Field>
  );
  return (
    <AdminLayout title="System Settings">
      <Card className="max-w-xl">
        <form onSubmit={(e) => { e.preventDefault(); patch(route('admin.settings.update')); }} className="space-y-4">
          {num('min_duration_minutes', 'Durasi minimal (menit)', 'Kosong = tanpa batas minimal')}
          {num('max_duration_minutes', 'Durasi maksimal (menit)', 'Kosong = tanpa batas maksimal')}
          {num('checkin_grace_minutes', 'Batas check-in setelah jam mulai (menit)', 'Default 10')}
          {num('checkin_open_before_minutes', 'Check-in dibuka sebelum jam mulai (menit)', 'Default 0')}
          <Button type="submit" disabled={processing}>Simpan</Button>
        </form>
      </Card>
    </AdminLayout>
  );
}
