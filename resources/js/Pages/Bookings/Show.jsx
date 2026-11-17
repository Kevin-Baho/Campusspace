import { router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Button, Card, Field, StatusBadge, fmtDate, fmtTime, inputCls } from '@/Components/UI';

function ReviewForm({ booking }) {
  const { data, setData, post, processing, errors } = useForm({ rating: 5, comment: '' });
  return (
    <Card className="mt-4">
      <h3 className="mb-3 font-semibold">Beri ulasan</h3>
      <form onSubmit={(e) => { e.preventDefault(); post(route('reviews.store', booking.id)); }} className="space-y-3">
        <Field label="Rating" error={errors.rating}>
          <select className={inputCls} value={data.rating} onChange={(e) => setData('rating', Number(e.target.value))}>{[5, 4, 3, 2, 1].map((n) => <option key={n} value={n}>{'★'.repeat(n)} ({n})</option>)}</select>
        </Field>
        <Field label="Komentar" error={errors.comment}><textarea className={inputCls} rows={3} maxLength={1000} value={data.comment} onChange={(e) => setData('comment', e.target.value)} /></Field>
        <Button type="submit" disabled={processing}>Kirim ulasan</Button>
      </form>
    </Card>
  );
}

export default function Show({ booking, canCancel }) {
  return (
    <AppLayout title="Detail Booking">
      <Card>
        <div className="flex items-start justify-between">
          <div><h2 className="text-lg font-semibold">{booking.unit.facility.name} · {booking.unit.code}</h2><p className="text-sm text-slate-500">{booking.purpose || 'Tanpa keterangan tujuan'}</p></div>
          <StatusBadge status={booking.status} />
        </div>
        <dl className="mt-4 grid gap-2 text-sm sm:grid-cols-2">
          <div><dt className="text-slate-500">Tanggal</dt><dd>{fmtDate(booking.starts_at)}</dd></div>
          <div><dt className="text-slate-500">Waktu</dt><dd>{fmtTime(booking.starts_at)}–{fmtTime(booking.ends_at)}</dd></div>
          <div><dt className="text-slate-500">Batas check-in</dt><dd>{fmtTime(booking.checkin_deadline_at)}</dd></div>
          <div><dt className="text-slate-500">Check-in</dt><dd>{booking.check_in ? fmtTime(booking.check_in.checked_in_at) : '-'}</dd></div>
        </dl>
        {canCancel && <Button variant="danger" className="mt-4" onClick={() => confirm('Batalkan booking ini?') && router.post(route('bookings.cancel', booking.id))}>Batalkan booking</Button>}
      </Card>
      {booking.status === 'completed' && (booking.review
        ? <Card className="mt-4 text-sm">Ulasan Anda: {'★'.repeat(booking.review.rating)} {booking.review.comment}</Card>
        : <ReviewForm booking={booking} />)}
    </AppLayout>
  );
}
