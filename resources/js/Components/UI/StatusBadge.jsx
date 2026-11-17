import { STATUS_LABEL } from '@/Utils/format';

const COLORS = {
    pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
    confirmed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
    checked_in: 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-200',
    completed: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
    cancelled: 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
    expired: 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
};

export default function StatusBadge({ status }) {
    return <span className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${COLORS[status] || COLORS.pending}`}>{STATUS_LABEL[status] || status}</span>;
}
