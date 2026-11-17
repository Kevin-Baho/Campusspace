import { router, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Button, Card, Empty, Pagination } from '@/Components/UI';

export default function Reviews({ reviews }) {
  const isAdmin = usePage().props.auth.user.role === 'admin';
  return (
    <AdminLayout title="Reviews">
      {reviews.data.length === 0 ? <Empty>Belum ada review.</Empty> : reviews.data.map((r) => (
        <Card key={r.id} className="mb-3 flex items-start justify-between gap-3 text-sm">
          <div><b>{r.facility?.name}</b> · {'★'.repeat(r.rating)}<p>{r.comment}</p><p className="text-xs text-slate-500">oleh {r.user?.name}</p></div>
          {isAdmin && <Button variant="danger" onClick={() => confirm('Hapus review?') && router.delete(route('admin.reviews.destroy', r.id), { preserveScroll: true })}>Hapus</Button>}
        </Card>))}
      <Pagination links={reviews.links} />
    </AdminLayout>
  );
}
