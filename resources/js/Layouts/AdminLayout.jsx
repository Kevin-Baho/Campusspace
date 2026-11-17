import { Head, usePage } from '@inertiajs/react';
import { BarChart3, Building2, CalendarCheck, Grid3x3, Home, QrCode, Settings, Star, Users } from 'lucide-react';
import Shell from '@/Components/Navigation/Shell';

export default function AdminLayout({ title, children }) {
  const isAdmin = usePage().props.auth.user?.role === 'admin';
  const items = [
    { href: route('admin.dashboard'), label: 'Dashboard', icon: BarChart3 },
    isAdmin && { href: route('admin.users.index'), label: 'Users', icon: Users },
    isAdmin && { href: route('admin.facilities.index'), label: 'Facilities', icon: Building2 },
    isAdmin && { href: route('admin.units.index'), label: 'Facility Units', icon: Grid3x3 },
    { href: route('admin.bookings.index'), label: 'Bookings', icon: CalendarCheck },
    { href: route('admin.qr.index'), label: 'QR Management', icon: QrCode },
    { href: route('admin.reviews.index'), label: 'Reviews', icon: Star },
    isAdmin && { href: route('admin.reports.index'), label: 'Reports', icon: BarChart3 },
    isAdmin && { href: route('admin.settings.index'), label: 'System Settings', icon: Settings },
    { href: route('dashboard'), label: 'Ke Aplikasi Mahasiswa', icon: Home },
  ].filter(Boolean);
  return (<><Head title={title} /><Shell title={title} items={items} brand="CampusSpace Admin">{children}</Shell></>);
}
