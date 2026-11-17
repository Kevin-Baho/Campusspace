import { Link, usePage } from '@inertiajs/react';
import { BookOpen, DoorOpen, Sparkles, Trees } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import { Badge, Button, Card, Empty, StatusBadge, fmtDate, fmtTime } from '@/Components/UI';

const cats = [['library', 'Library', BookOpen], ['saung', 'Saung', Trees], ['room', 'Rooms', DoorOpen]];

export default function Index({ active, recent, availability, notifications }) {
  const { auth } = usePage().props;
  return (
    <AppLayout title="Dashboard">
      <div className="mb-6 rounded-2xl bg-gradient-to-r from-blue-600 to-violet-600 p-6 text-white">
        <p className="text-sm opacity-80">Selamat datang kembali</p>
        <h2 className="text-2xl font-bold">{auth.user.name}</h2>
        <Button as={Link} href={route('ai.index')} variant="ghost" className="mt-4 !border-white/60 !text-white hover:!bg-white/10"><Sparkles size={16} /> Tanya AI Assistant</Button>
      </div>

      <div className="mb-6 grid gap-4 sm:grid-cols-3">
        {cats.map(([k, label, Icon]) => (
          <Link key={k} href={route('facilities.index', k)}>
            <Card className="transition hover:shadow-md">
              <Icon className="mb-2 text-violet-600" />
              <div className="font-semibold">{label}</div>
              <div className="text-sm text-slate-500">{availability[k].available} dari {availability[k].total} tersedia sekarang</div>
            </Card>
          </Link>
        ))}
      </div>

      <div className="grid gap-4 lg:grid-cols-2">
        <Card>
          <h3 className="mb-3 font-semibold">Booking aktif</h3>
          {active ? (
            <div className="space-y-2 text-sm">
              <div className="flex items-center justify-between"><b>{active.unit.code}</b><StatusBadge status={active.status} /></div>
              <div>{fmtDate(active.starts_at)} · {fmtTime(active.starts_at)}–{fmtTime(active.ends_at)}</div>
              <div className="flex gap-2 pt-2">
                <Button as={Link} href={route('bookings.show', active.id)}>Detail</Button>
                {active.status === 'confirmed' && <Button as={Link} href={route('checkin.index')} variant="ghost">Check-in</Button>}
              </div>
            </div>
          ) : <Empty>Belum ada booking aktif.</Empty>}
        </Card>

        <Card>
          <div className="mb-3 flex justify-between"><h3 className="font-semibold">Notifikasi</h3><Link className="text-sm text-blue-600" href={route('notifications.index')}>Semua</Link></div>
          {notifications.length ? notifications.map((n) => (
            <div key={n.id} className="border-b border-slate-100 py-2 text-sm last:border-0 dark:border-slate-700">
              <div className="flex items-center gap-2">{!n.read_at && <Badge tone="red">Baru</Badge>}<b>{n.title}</b></div>
              <div className="text-slate-500">{n.message}</div>
            </div>
          )) : <Empty>Tidak ada notifikasi.</Empty>}
        </Card>

        <Card className="lg:col-span-2">
          <h3 className="mb-3 font-semibold">Riwayat terbaru</h3>
          {recent.length ? recent.map((b) => (
            <Link key={b.id} href={route('bookings.show', b.id)} className="flex items-center justify-between border-b border-slate-100 py-2 text-sm last:border-0 dark:border-slate-700">
              <span><b>{b.unit.code}</b> · {fmtDate(b.starts_at)} {fmtTime(b.starts_at)}–{fmtTime(b.ends_at)}</span><StatusBadge status={b.status} />
            </Link>
          )) : <Empty>Belum ada riwayat booking.</Empty>}
        </Card>
      </div>
    </AppLayout>
  );
}
