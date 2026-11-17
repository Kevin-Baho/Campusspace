import AdminLayout from '@/Layouts/AdminLayout';
import BarChart from '@/Components/Cards/Chart';
import { Card } from '@/Components/UI';

export default function Dashboard({ stats, trend, usage }) {
  const labels = { users: 'Pengguna', facilities: 'Fasilitas', units: 'Unit', bookings: 'Total booking', active: 'Booking aktif', completed: 'Selesai' };
  return (
    <AdminLayout title="Dashboard">
      <div className="mb-6 grid gap-4 sm:grid-cols-3">
        {Object.entries(stats).map(([k, v]) => <Card key={k}><div className="text-sm text-slate-500">{labels[k]}</div><div className="text-3xl font-bold">{v}</div></Card>)}
      </div>
      <div className="grid gap-4 lg:grid-cols-2">
        <Card><h3 className="mb-2 font-semibold">Tren booking (14 hari)</h3><BarChart data={trend.map((t) => ({ label: String(t.d).slice(8, 10), value: Number(t.total) }))} /></Card>
        <Card><h3 className="mb-2 font-semibold">Penggunaan per kategori</h3><BarChart data={usage.map((u) => ({ label: u.category, value: Number(u.total) }))} /></Card>
      </div>
    </AdminLayout>
  );
}
