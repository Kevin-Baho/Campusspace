import { router } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Button, Card, Pagination, inputCls } from '@/Components/UI';

export default function Users({ users, q }) {
  const [s, setS] = useState(q || '');
  return (
    <AdminLayout title="Users">
      <form className="mb-4 flex gap-2" onSubmit={(e) => { e.preventDefault(); router.get(route('admin.users.index'), { q: s }, { preserveState: true }); }}>
        <input className={inputCls} placeholder="Cari nama/email" value={s} onChange={(e) => setS(e.target.value)} /><Button type="submit">Cari</Button>
      </form>
      <Card className="overflow-x-auto"><table className="w-full text-left text-sm">
        <thead className="text-slate-500"><tr><th className="p-2">Nama</th><th>Email</th><th>Role</th></tr></thead>
        <tbody>{users.data.map((u) => (
          <tr key={u.id} className="border-t border-slate-100 dark:border-slate-700"><td className="p-2">{u.name}</td><td>{u.email}</td>
            <td><select className={inputCls + ' !w-auto'} value={u.role} onChange={(e) => router.patch(route('admin.users.update', u.id), { role: e.target.value }, { preserveScroll: true })}>
              {['student', 'staff', 'admin'].map((r) => <option key={r}>{r}</option>)}</select></td></tr>))}</tbody>
      </table></Card>
      <Pagination links={users.links} />
    </AdminLayout>
  );
}
