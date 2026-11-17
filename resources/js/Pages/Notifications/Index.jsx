import { router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Badge, Button, Card, Empty, Pagination } from '@/Components/UI';

export default function Index({ items }) {
  return (
    <AppLayout title="Notifications">
      <div className="mb-4 flex justify-end"><Button variant="ghost" onClick={() => router.post(route('notifications.readAll'), {}, { preserveScroll: true })}>Tandai semua dibaca</Button></div>
      {items.data.length === 0 ? <Empty>Belum ada notifikasi.</Empty> : (
        <div className="space-y-2">
          {items.data.map((n) => (
            <Card key={n.id} className={`flex items-start justify-between gap-3 ${n.read_at ? 'opacity-70' : ''}`}>
              <div className="text-sm"><div className="flex items-center gap-2">{!n.read_at && <Badge tone="red">Baru</Badge>}<b>{n.title}</b></div><p className="text-slate-500">{n.message}</p><p className="text-xs text-slate-400">{new Date(n.created_at).toLocaleString('id-ID')}</p></div>
              {!n.read_at && <Button variant="ghost" onClick={() => router.post(route('notifications.read', n.id), {}, { preserveScroll: true })}>Tandai dibaca</Button>}
            </Card>
          ))}
        </div>
      )}
      <Pagination links={items.links} />
    </AppLayout>
  );
}
