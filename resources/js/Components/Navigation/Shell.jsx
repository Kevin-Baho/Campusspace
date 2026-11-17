import { Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { Menu, Moon, Sun, X } from 'lucide-react';

export function useDark() {
  const [dark, setDark] = useState(() => typeof localStorage !== 'undefined' && localStorage.getItem('theme') === 'dark');
  useEffect(() => {
    document.documentElement.classList.toggle('dark', dark);
    localStorage.setItem('theme', dark ? 'dark' : 'light');
  }, [dark]);
  return [dark, setDark];
}

export default function Shell({ title, items, children, brand }) {
  const { url, props } = usePage();
  const [open, setOpen] = useState(false);
  const [dark, setDark] = useDark();
  const flash = props.flash || {};
  const active = (h) => url === h || (h.length > 1 && url.startsWith(h + '/')) || (h.length > 1 && url.startsWith(h + '?'));

  const nav = (
    <nav className="space-y-1">
      {items.map(({ href, label, icon: Icon, badge }) => (
        <Link key={href} href={href} onClick={() => setOpen(false)}
          className={`flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium ${active(href) ? 'bg-gradient-to-r from-blue-600 to-violet-600 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'}`}>
          <Icon size={18} /> <span className="flex-1">{label}</span>
          {badge > 0 && <span className="rounded-full bg-rose-500 px-2 text-xs text-white">{badge}</span>}
        </Link>
      ))}
    </nav>
  );

  return (
    <div className="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-slate-100">
      <aside className="fixed inset-y-0 left-0 hidden w-64 overflow-y-auto border-r border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900 lg:block">
        <div className="mb-6 bg-gradient-to-r from-blue-600 to-violet-600 bg-clip-text text-xl font-bold text-transparent">{brand}</div>
        {nav}
      </aside>

      {open && (
        <div className="fixed inset-0 z-40 lg:hidden">
          <div className="absolute inset-0 bg-black/40" onClick={() => setOpen(false)} />
          <aside className="absolute inset-y-0 left-0 w-64 overflow-y-auto bg-white p-4 dark:bg-slate-900">
            <button className="mb-4" onClick={() => setOpen(false)} aria-label="Tutup menu"><X /></button>
            {nav}
          </aside>
        </div>
      )}

      <div className="lg:pl-64">
        <header className="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur dark:border-slate-700 dark:bg-slate-900/80">
          <div className="flex items-center gap-3">
            <button className="lg:hidden" onClick={() => setOpen(true)} aria-label="Buka menu"><Menu /></button>
            <h1 className="text-lg font-semibold">{title}</h1>
          </div>
          <div className="flex items-center gap-3 text-sm">
            <button onClick={() => setDark(!dark)} aria-label="Ganti tema" className="rounded-lg p-2 hover:bg-slate-100 dark:hover:bg-slate-800">{dark ? <Sun size={18} /> : <Moon size={18} />}</button>
            <span className="hidden sm:inline">{props.auth.user?.name}</span>
            <Link href={route('logout')} method="post" as="button" className="text-rose-600">Keluar</Link>
          </div>
        </header>
        <main className="mx-auto max-w-6xl p-4 sm:p-6">
          {flash.success && <div className="mb-4 rounded-xl bg-emerald-100 p-3 text-sm text-emerald-800">{flash.success}</div>}
          {flash.error && <div className="mb-4 rounded-xl bg-rose-100 p-3 text-sm text-rose-800">{flash.error}</div>}
          {children}
        </main>
      </div>
    </div>
  );
}
