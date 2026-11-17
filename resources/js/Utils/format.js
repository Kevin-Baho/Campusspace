export const fmtTime = (iso) => (iso ? new Date(iso).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '-');
export const fmtDate = (iso) => (iso ? new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-');
export const CATEGORY_LABEL = { library: 'Library', saung: 'Saung', room: 'Room' };
export const STATUS_LABEL = {
    pending: 'Pending', confirmed: 'Dikonfirmasi', checked_in: 'Check-in', completed: 'Selesai', cancelled: 'Dibatalkan', expired: 'Kedaluwarsa',
};
