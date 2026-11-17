import { router, useForm } from '@inertiajs/react';
import { Send, Trash2 } from 'lucide-react';
import { useEffect, useRef } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { Button, Card, Empty, inputCls } from '@/Components/UI';

export default function Chat({ messages }) {
  const { data, setData, post, processing, errors, reset } = useForm({ message: '' });
  const end = useRef(null);
  useEffect(() => end.current?.scrollIntoView({ behavior: 'smooth' }), [messages]);
  const send = (e) => { e.preventDefault(); if (!data.message.trim()) return; post(route('ai.message'), { preserveScroll: true, onSuccess: () => reset() }); };

  return (
    <AppLayout title="AI Assistant">
      <Card className="flex h-[70vh] flex-col">
        <div className="flex-1 space-y-3 overflow-y-auto pr-1">
          {messages.length === 0 && <Empty>Tanyakan, mis. “Ruangan untuk 8 orang dengan proyektor yang kosong sekarang?”. AI hanya memberi saran; booking tetap Anda lakukan lewat menu.</Empty>}
          {messages.map((m) => (
            <div key={m.id} className={`flex ${m.role === 'user' ? 'justify-end' : 'justify-start'}`}>
              <div className={`max-w-[85%] whitespace-pre-wrap rounded-2xl px-4 py-2 text-sm ${m.role === 'user' ? 'bg-gradient-to-r from-blue-600 to-violet-600 text-white' : 'bg-slate-100 dark:bg-slate-700'}`}>{m.content}</div>
            </div>
          ))}
          {processing && <div className="text-sm text-slate-500">AI sedang mengetik…</div>}
          <div ref={end} />
        </div>
        {errors.message && <p className="text-sm text-rose-600">{errors.message}</p>}
        <form onSubmit={send} className="mt-3 flex gap-2">
          <input className={inputCls} placeholder="Tulis pertanyaan…" maxLength={1000} value={data.message} onChange={(e) => setData('message', e.target.value)} />
          <Button type="submit" disabled={processing} aria-label="Kirim"><Send size={16} /></Button>
          <Button type="button" variant="ghost" aria-label="Hapus percakapan" onClick={() => confirm('Hapus percakapan?') && router.delete(route('ai.clear'))}><Trash2 size={16} /></Button>
        </form>
      </Card>
    </AppLayout>
  );
}
