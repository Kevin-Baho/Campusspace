import { Link } from '@inertiajs/react';
import { MapPin, Star, Users } from 'lucide-react';
import { Badge, Button, Card } from '@/Components/UI';

export default function UnitCard({ unit, rating, onView }) {
  const f = unit.facility;
  return (
    <Card className="flex flex-col gap-3">
      <div className="flex items-start justify-between">
        <div>
          <div className="font-semibold">{unit.name || f.name} <span className="text-slate-400">· {unit.code}</span></div>
          <div className="mt-1 flex flex-wrap gap-3 text-xs text-slate-500">
            <span className="flex items-center gap-1"><MapPin size={14} />{[unit.floor && `Lt ${unit.floor}`, unit.location || f.location].filter(Boolean).join(' · ') || '-'}</span>
            {(unit.capacity || f.capacity) && <span className="flex items-center gap-1"><Users size={14} />{unit.capacity || f.capacity} orang</span>}
            {rating && <span className="flex items-center gap-1"><Star size={14} />{Number(rating.avg_rating).toFixed(1)} ({rating.total})</span>}
          </div>
        </div>
        <Badge tone={unit.available ? 'green' : 'red'}>{unit.available ? 'Tersedia' : 'Tidak tersedia'}</Badge>
      </div>
      <div className="flex flex-wrap gap-1">{f.amenities?.map((a) => <Badge key={a.id} tone="blue">{a.name}</Badge>)}</div>
      <div className="mt-auto flex gap-2">
        {onView && <Button variant="ghost" onClick={() => onView(unit)}>Lihat lokasi</Button>}
        {unit.available
          ? <Button as={Link} href={route('bookings.create', { unit: unit.id })}>Pesan</Button>
          : <Button disabled>Penuh</Button>}
      </div>
    </Card>
  );
}
