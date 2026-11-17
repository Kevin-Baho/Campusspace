import { Link, usePage } from '@inertiajs/react';
import { Moon, Sun, LogOut, Sparkles } from 'lucide-react';
import { useEffect, useState } from 'react';

function useDark() {
    const [dark, setDark] = useState(() => (typeof window !== 'undefined' ? localStorage.getItem('cs-theme') === 'dark' : false));
    useEffect(() => {
        document.documentElement.classList.toggle('dark', dark);
        try { localStorage.setItem('cs-theme', dark ? 'dark' : 'light'); } catch (e) {}
    }, [dark]);
    return [dark, setDark];
}

export default function Shell({ nav, title, brandSuffix, children, switchLink }) {
    const { auth, unread, flash } = usePage().props;
    const [dark, setDark] = useDark();
    const url = typeof window !== 'undefined' ? window.location.pathname : '';
    const items = nav.filter((n) => !n.adminOnly || auth.user.role === 'admin');

    const isActive = (n) => {
        try {
            if (n.match) return route().current(n.active) && url.endsWith('/' + n.match);
            return route().current(n.active);
        } catch (e) { return false; }
    };
    const href = (n) => route(n.route, n.params);

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-violet-50/40 text-gray-900 dark:from-gray-950 dark:via-gray-900 dark:to-gray-900 dark:text-gray-100">
            <aside className="fixed inset-y-0 left-0 hidden w-64 flex-col border-r border-gray-200 bg-white/90 p-4 backdrop-blur lg:flex dark:border-gray-800 dark:bg-gray-900/90">
                <div className="mb-6 flex items-center gap-2 px-2 text-lg font-bold">
                    <span className="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 text-white"><Sparkles size={18} /></span>
                    CampusSpace{brandSuffix && <span className="text-xs font-medium text-violet-600">{brandSuffix}</span>}
                </div>
                <nav className="flex-1 space-y-1 overflow-y-auto">
                    {items.map((n) => (
                        <Link key={n.name} href={href(n)} className={`flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium ${isActive(n) ? 'bg-gradient-to-r from-blue-600 to-violet-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800'}`}>
                            <n.icon size={18} /> <span className="flex-1">{n.name}</span>
                            {n.badge && unread > 0 && <span className="rounded-full bg-rose-500 px-2 text-xs text-white">{unread}</span>}
                        </Link>
                    ))}
                </nav>
                {switchLink}
            </aside>

            <div className="lg:pl-64">
                <header className="sticky top-0 z-20 flex items-center justify-between border-b border-gray-200 bg-white/80 px-4 py-3 backdrop-blur dark:border-gray-800 dark:bg-gray-900/80">
                    <h1 className="text-lg font-semibold">{title}</h1>
                    <div className="flex items-center gap-2">
                        <span className="hidden text-sm text-gray-500 sm:inline">{auth.user.name}</span>
                        <button onClick={() => setDark(!dark)} aria-label="Ganti tema" className="rounded-xl p-2 hover:bg-gray-100 dark:hover:bg-gray-800">{dark ? <Sun size={18} /> : <Moon size={18} />}</button>
                        <Link href={route('logout')} method="post" as="button" aria-label="Keluar" className="rounded-xl p-2 hover:bg-gray-100 dark:hover:bg-gray-800"><LogOut size={18} /></Link>
                    </div>
                </header>

                <main className="mx-auto max-w-6xl p-4 pb-24 lg:pb-8">
                    {flash?.success && <div role="status" className="mb-4 rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">{flash.success}</div>}
                    {flash?.error && <div role="alert" className="mb-4 rounded-xl bg-rose-100 px-4 py-3 text-sm text-rose-800 dark:bg-rose-900/40 dark:text-rose-200">{flash.error}</div>}
                    {children}
                </main>
            </div>

            <nav className="fixed inset-x-0 bottom-0 z-30 flex justify-around border-t border-gray-200 bg-white/95 py-1 backdrop-blur lg:hidden dark:border-gray-800 dark:bg-gray-900/95">
                {items.slice(0, 5).map((n) => (
                    <Link key={n.name} href={href(n)} className={`flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] ${isActive(n) ? 'text-violet-600' : 'text-gray-500'}`}>
                        <n.icon size={20} />{n.name.split(' ')[0]}
                    </Link>
                ))}
                <Link href={route(nav[0].route === 'dashboard' ? 'notifications.index' : 'admin.dashboard')} className="relative flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] text-gray-500">
                    <span className="text-lg leading-5">⋯</span>Lainnya
                </Link>
            </nav>
        </div>
    );
}
