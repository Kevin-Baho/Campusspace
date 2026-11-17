import { router } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Button, Card, inputCls } from '@/Components/UI';

export default function Reports({ perUnit, from, to }) {
  const [r, setR] = useState({ from, to });
  return (
    <AdminLayout title="Reports">
      <form className="mb-4 flex flex-wrap items-end gap-2" onSubmit={(e) => { e.preventDefault(); router.get(route('admin.reports.index'), r, { preserveState: true }); }}>
        <input type="date" className={inputCls + ' !w-auto'} value={r.from} onChange={(e) => setR({ ...r, from: e.target.value })} />
        <input type="date" className={inputCls + ' !w-auto'} value={r.to} onChange={(e) => setR({ ...r, to: e.target.value })} />
        <Button type="submit">Terapkan</Button>
      </form>
      <Card className="overflow-x-auto"><table className="w-full text-left text-sm">
        <thead className="text-slate-500"><tr><th className="p-2">Unit</th><th>Total booking</th><th>Hadir</th><th>Kedaluwarsa</th></tr></thead>
        <tbody>{perUnit.map((u) => <tr key={u.code} className="border-t border-slate-100 dark:border-slate-700"><td className="p-2">{u.code}</td><td>{u.total}</td><td>{u.attended}</td><td>{u.expired}</td></tr>)}</tbody>
      </table></Card>
    </AdminLayout>
  );
}
