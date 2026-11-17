import { Link } from '@inertiajs/react';

export const Card = ({ className = '', children }) => (
  <div className={`rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 ${className}`}>{children}</div>
);

const tones = {
  blue: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
  violet: 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-200',
  green: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
  red: 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
  amber: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
  gray: 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
};
export const Badge = ({ tone = 'gray', children }) => (
  <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${tones[tone]}`}>{children}</span>
);

const statusMap = {
  pending: ['amber', 'Menunggu'], confirmed: ['blue', 'Dikonfirmasi'], checked_in: ['violet', 'Check-in'],
  completed: ['green', 'Selesai'], cancelled: ['gray', 'Dibatalkan'], expired: ['red', 'Kedaluwarsa'],
};
export const StatusBadge = ({ status }) => {
  const [tone, label] = statusMap[status] || ['gray', status];
  return <Badge tone={tone}>{label}</Badge>;
};

export const Button = ({ as: As = 'button', variant = 'primary', className = '', ...p }) => {
  const v = {
    primary: 'bg-gradient-to-r from-blue-600 to-violet-600 text-white hover:opacity-90',
    ghost: 'border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700',
    danger: 'bg-rose-600 text-white hover:bg-rose-700',
  }[variant];
  return <As {...p} className={`inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition disabled:opacity-50 ${v} ${className}`} />;
};

export const Field = ({ label, error, children }) => (
  <label className="block text-sm">
    <span className="mb-1 block font-medium text-slate-700 dark:text-slate-200">{label}</span>
    {children}
    {error && <span className="mt-1 block text-rose-600">{error}</span>}
  </label>
);
export const inputCls = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100';

export const Empty = ({ children }) => (
  <div className="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-600">{children}</div>
);

export const Pagination = ({ links = [] }) => links.length > 3 ? (
  <div className="mt-4 flex flex-wrap gap-1">
    {links.map((l, i) => l.url
      ? <Link key={i} href={l.url} preserveScroll className={`rounded-lg px-3 py-1 text-sm ${l.active ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-700 dark:text-slate-200'}`} dangerouslySetInnerHTML={{ __html: l.label }} />
      : <span key={i} className="px-3 py-1 text-sm text-slate-400" dangerouslySetInnerHTML={{ __html: l.label }} />)}
  </div>
) : null;

export const fmtTime = (iso) => iso ? new Date(iso).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
export const fmtDate = (iso) => iso ? new Date(iso).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '';
