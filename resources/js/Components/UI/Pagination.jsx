import { Link } from '@inertiajs/react';

export default function Pagination({ links = [] }) {
    if (links.length <= 3) return null;
    return (
        <div className="mt-4 flex flex-wrap gap-1">
            {links.map((l, i) => (
                <Link key={i} href={l.url || '#'} preserveScroll dangerouslySetInnerHTML={{ __html: l.label }}
                    className={`rounded-lg px-3 py-1 text-sm ${l.active ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200'} ${!l.url ? 'pointer-events-none opacity-40' : ''}`} />
            ))}
        </div>
    );
}
