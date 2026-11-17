import { router, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Badge, Button, Card, Field, Pagination, inputCls } from '@/Components/UI';

export default function Facilities({ facilities }) {
  const { data, setData, post, processing, errors, reset } = useForm({ category: 'room', name: '', location: '', floor: '', capacity: '', description: '', is_active: true, amenities: [], amenitiesText: '' });
  const submit = (e) => {
    e.preventDefault();
    router.post(route('admin.facilities.store'), { ...data, amenities: data.amenitiesText.split(',').map((x) => x.trim()).filter(Boolean) }, { onSuccess: () => reset() });
  };
  return (
    <AdminLayout title="Facilities">
      <Card className="mb-6">
        <h3 className="mb-3 font-semibold">Tambah fasilitas</h3>
        <form onSubmit={submit} className="grid gap-3 sm:grid-cols-3">
          <Field label="Kategori"><select className={inputCls} value={data.category} onChange={(e) => setData('category', e.target.value)}><option value="library">library</option><option value="saung">saung</option><option value="room">room</option></select></Field>
          <Field label="Nama" error={errors.name}><input className={inputCls} value={data.name} onChange={(e) => setData('name', e.target.value)} required /></Field>
          <Field label="Lokasi"><input className={inputCls} value={data.location} onChange={(e) => setData('location', e.target.value)} /></Field>
          <Field label="Lantai"><input className={inputCls} value={data.floor} onChange={(e) => setData('floor', e.target.value)} /></Field>
          <Field label="Kapasitas"><input type="number" min="1" className={inputCls} value={data.capacity} onChange={(e) => setData('capacity', e.target.value)} /></Field>
          <Field label="Fasilitas (pisah koma)"><input className={inputCls} value={data.amenitiesText} onChange={(e) => setData('amenitiesText', e.target.value)} /></Field>
          <div className="sm:col-span-3"><Button type="submit" disabled={processing}>Simpan</Button></div>
        </form>
      </Card>
      <Card className="overflow-x-auto"><table className="w-full text-left text-sm">
        <thead className="text-slate-500"><tr><th className="p-2">Nama</th><th>Kategori</th><th>Unit</th><th>Status</th><th></th></tr></thead>
        <tbody>{facilities.data.map((f) => (
          <tr key={f.id} className="border-t border-slate-100 dark:border-slate-700">
            <td className="p-2">{f.name}<div className="text-xs text-slate-500">{f.amenities.map((a) => a.name).join(', ')}</div></td><td>{f.category}</td><td>{f.units_count}</td>
            <td><Badge tone={f.is_active ? 'green' : 'gray'}>{f.is_active ? 'Aktif' : 'Nonaktif'}</Badge></td>
            <td className="space-x-2 text-right">
              <Button variant="ghost" onClick={() => { const name = prompt('Nama fasilitas', f.name); if (name) router.patch(route('admin.facilities.update', f.id), { category: f.category, name, is_active: f.is_active, amenities: f.amenities.map((a) => a.name) }, { preserveScroll: true }); }}>Ubah</Button>
              <Button variant="ghost" onClick={() => router.patch(route('admin.facilities.update', f.id), { category: f.category, name: f.name, is_active: !f.is_active, amenities: f.amenities.map((a) => a.name) }, { preserveScroll: true })}>{f.is_active ? 'Nonaktifkan' : 'Aktifkan'}</Button>
            </td></tr>))}</tbody>
      </table></Card>
      <Pagination links={facilities.links} />
    </AdminLayout>
  );
}
