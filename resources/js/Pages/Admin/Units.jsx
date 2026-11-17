import { router, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Badge, Button, Card, Field, Pagination, inputCls } from '@/Components/UI';

export default function Units({ units, facilities }) {
  const { data, setData, processing, errors, reset } = useForm({ facility_id: facilities[0]?.id || '', code: '', name: '', capacity: '', floor: '', location: '', status: 'available', imagesText: '' });
  const submit = (e) => {
    e.preventDefault();
    const view_images = data.imagesText.split('\n').map((x) => x.trim()).filter(Boolean);
    router.post(route('admin.units.store'), { ...data, view_images, is_active: true }, { onSuccess: () => reset() });
  };
  const toggle = (u) => router.patch(route('admin.units.update', u.id), { facility_id: u.facility_id, code: u.code, name: u.name, capacity: u.capacity, floor: u.floor, location: u.location, view_images: u.view_images, status: u.status === 'available' ? 'maintenance' : 'available', is_active: u.is_active }, { preserveScroll: true });
  return (
    <AdminLayout title="Facility Units">
      <Card className="mb-6">
        <form onSubmit={submit} className="grid gap-3 sm:grid-cols-3">
          <Field label="Fasilitas"><select className={inputCls} value={data.facility_id} onChange={(e) => setData('facility_id', e.target.value)}>{facilities.map((f) => <option key={f.id} value={f.id}>{f.name} ({f.category})</option>)}</select></Field>
          <Field label="Kode unik" error={errors.code}><input className={inputCls} value={data.code} onChange={(e) => setData('code', e.target.value)} required /></Field>
          <Field label="Nama"><input className={inputCls} value={data.name} onChange={(e) => setData('name', e.target.value)} /></Field>
          <Field label="Kapasitas"><input type="number" min="1" className={inputCls} value={data.capacity} onChange={(e) => setData('capacity', e.target.value)} /></Field>
          <Field label="Lantai"><input className={inputCls} value={data.floor} onChange={(e) => setData('floor', e.target.value)} /></Field>
          <Field label="Lokasi"><input className={inputCls} value={data.location} onChange={(e) => setData('location', e.target.value)} /></Field>
          <div className="sm:col-span-3"><Field label="URL foto sudut saung (satu per baris, urutan maju→mundur)" error={errors.view_images}><textarea rows={2} className={inputCls} value={data.imagesText} onChange={(e) => setData('imagesText', e.target.value)} /></Field></div>
          <div><Button type="submit" disabled={processing}>Tambah unit</Button></div>
        </form>
      </Card>
      <Card className="overflow-x-auto"><table className="w-full text-left text-sm">
        <thead className="text-slate-500"><tr><th className="p-2">Kode</th><th>Fasilitas</th><th>Kapasitas</th><th>Status</th><th></th></tr></thead>
        <tbody>{units.data.map((u) => (
          <tr key={u.id} className="border-t border-slate-100 dark:border-slate-700"><td className="p-2">{u.code}</td><td>{u.facility?.name}</td><td>{u.capacity ?? '-'}</td>
            <td><Badge tone={!u.is_active ? 'gray' : u.status === 'available' ? 'green' : 'amber'}>{!u.is_active ? 'Nonaktif' : u.status}</Badge></td>
            <td className="space-x-2 text-right"><Button variant="ghost" onClick={() => toggle(u)}>{u.status === 'available' ? 'Set maintenance' : 'Set available'}</Button>
              <Button variant="danger" onClick={() => confirm('Nonaktifkan unit?') && router.delete(route('admin.units.destroy', u.id), { preserveScroll: true })}>Nonaktifkan</Button></td></tr>))}</tbody>
      </table></Card>
      <Pagination links={units.links} />
    </AdminLayout>
  );
}
