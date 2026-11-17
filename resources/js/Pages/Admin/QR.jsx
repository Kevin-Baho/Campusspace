import { router, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Button, Card, Pagination } from '@/Components/UI';

export default function QR({ units }) {
  const isAdmin = usePage().props.auth.user.role === 'admin';
  return (
    <AdminLayout title="QR Management">
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {units.data.map((u) => (
          <Card key={u.id} className="text-center">
            <div className="mx-auto w-fit rounded-lg bg-white p-2" dangerouslySetInnerHTML={{ __html: u.qr_svg }} />
            <div className="mt-2 font-semibold">{u.code}</div><div className="text-xs text-slate-500">{u.facility?.name}</div>
            <div className="mt-3 flex justify-center gap-2">
              <Button variant="ghost" onClick={() => window.print()}>Cetak</Button>
              {isAdmin && <Button variant="danger" onClick={() => confirm('QR lama akan tidak berlaku. Lanjut?') && router.post(route('admin.qr.regenerate', u.id), {}, { preserveScroll: true })}>Buat ulang</Button>}
            </div>
          </Card>
        ))}
      </div>
      <Pagination links={units.links} />
    </AdminLayout>
  );
}
