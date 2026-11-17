import { Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Button, Card, Empty, Pagination, StatusBadge, fmtDate, fmtTime } from '@/Components/UI';

export default function Index({ mode, bookings }) {
  const rows = mode === 'history' ? bookings.data : bookings;
  return (
    <AppLayout title={mode === 'history' ? 'Booking History' : 'My Booking'}>
      {rows.length === 0 ? <Empty>{mode === 'history' ? 'Belum ada riwayat.' : 'Tidak ada booking aktif. Mulai dari menu Library, Saung, atau Room.'}</Empty> : (
        <div className="space-y-3">
          {rows.map((b) => (
            <Card key={b.id} className="flex flex-wrap items-center justify-between gap-3">
              <Link href={route('bookings.show', b.id)} className="text-sm">
                <div className="font-semibold">{b.unit.facility.name} · {b.unit.code}</div>
                <div className="text-slate-500">{fmtDate(b.starts_at)} · {fmtTime(b.starts_at)}–{fmtTime(b.ends_at)}</div>
              </Link>
              <div className="flex items-center gap-2">
                <StatusBadge status={b.status} />
                {b.status === 'confirmed' && <Button as={Link} href={route('checkin.index')} variant="ghost">Check-in</Button>}
                {['pending', 'confirmed'].includes(b.status) && (
                  <Button variant="danger" onClick={() => confirm('Batalkan booking ini?') && router.post(route('bookings.cancel', b.id), {}, { preserveScroll: true })}>Batalkan</Button>
                )}
              </div>
            </Card>
          ))}
        </div>
      )}
      {mode === 'history' && <Pagination links={bookings.links} />}
    </AppLayout>
  );
}
