import { useForm } from '@inertiajs/react';
import { Html5Qrcode } from 'html5-qrcode';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Button, Card } from '@/Components/UI';

const tokenFrom = (text) => text.trim().split('/').filter(Boolean).pop();

export default function Scan({ token }) {
  const { data, setData, post, processing, errors } = useForm({ token: token || '' });
  const [camErr, setCamErr] = useState(null);
  const scanner = useRef(null);
  const submitted = useRef(false);

  const submit = (t) => { if (submitted.current) return; submitted.current = true; setData('token', t); router_post(t); };
  const router_post = (t) => post(route('checkin.store'), { data: { token: t }, onFinish: () => { submitted.current = false; } });

  useEffect(() => {
    if (token) return; // dibuka dari QR: tampilkan tombol konfirmasi saja
    const s = new Html5Qrcode('qr-reader');
    scanner.current = s;
    s.start({ facingMode: 'environment' }, { fps: 10, qrbox: 240 }, (text) => { s.stop().catch(() => {}); submit(tokenFrom(text)); })
      .catch(() => setCamErr('Kamera tidak dapat diakses. Izinkan akses kamera atau pindai QR dengan kamera bawaan HP.'));
    return () => { s.isScanning && s.stop().catch(() => {}); };
  }, []);

  return (
    <AppLayout title="QR Check-in">
      <Card className="mx-auto max-w-md">
        {token ? (
          <div className="space-y-4 text-center">
            <p>QR unit terdeteksi. Konfirmasi check-in untuk booking Anda?</p>
            <Button className="w-full" disabled={processing} onClick={() => router_post(token)}>Check-in sekarang</Button>
          </div>
        ) : (
          <>
            <div id="qr-reader" className="overflow-hidden rounded-xl" />
            {camErr && <p className="mt-3 text-sm text-amber-600">{camErr}</p>}
            <p className="mt-3 text-center text-sm text-slate-500">Arahkan kamera ke QR Code di unit yang Anda pesan.</p>
          </>
        )}
        {errors.token && <p className="mt-3 rounded-xl bg-rose-100 p-3 text-sm text-rose-800">{errors.token}</p>}
      </Card>
    </AppLayout>
  );
}
