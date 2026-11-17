import { router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Button, Card, Pagination, StatusBadge, fmtDate, fmtTime, inputCls } from '@/Components/UI';

export default function Bookings({ bookings, status }) {
  return (
    <AdminLayout title="Bookings">
      <select className={inputCls + ' mb-4 !w-auto'} value={status} onChange={(e) => router.get(route('admin.bookings.index'), e.target.value ? { status: e.target.value } : {}, { preserveState: true })}>
        <option value="">Semua status</option>{['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'expired'].map((s) => <option key={s}>{s}</option>)}
      </select>
      <Card className="overflow-x-auto"><table className="w-full text-left text-sm">
        <thead className="text-slate-500"><tr><th className="p-2">Pengguna</th><th>Unit</th><th>Waktu</th><th>Status</th><th></th></tr></thead>
        <tbody>{bookings.data.map((b) => (
          <tr key={b.id} className="border-t border-slate-100 dark:border-slate-700"><td className="p-2">{b.user.name}<div className="text-xs text-slate-500">{b.user.email}</div></td><td>{b.unit.code}</td>
            <td>{fmtDate(b.starts_at)} {fmtTime(b.starts_at)}–{fmtTime(b.ends_at)}</td><td><StatusBadge status={b.status} /></td>
            <td className="text-right">{['pending', 'confirmed'].includes(b.status) && <Button variant="danger" onClick={() => confirm('Batalkan booking?') && router.post(route('admin.bookings.cancel', b.id), {}, { preserveScroll: true })}>Batalkan</Button>}</td></tr>))}</tbody>
      </table></Card>
      <Pagination links={bookings.links} />
    </AdminLayout>
  );
}
