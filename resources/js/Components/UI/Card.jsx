export default function Card({ title, action, children, className = '' }) {
    return (
        <section className={`rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 ${className}`}>
            {(title || action) && (
                <div className="mb-3 flex items-center justify-between gap-2">
                    <h2 className="text-base font-semibold text-gray-900 dark:text-gray-100">{title}</h2>
                    {action}
                </div>
            )}
            {children}
        </section>
    );
}

export function Empty({ children }) {
    return <p className="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">{children}</p>;
}

export const btn = 'inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-violet-600 px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90 disabled:opacity-50';
export const btnGhost = 'inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700';
export const input = 'w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-violet-500 focus:ring-violet-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100';
