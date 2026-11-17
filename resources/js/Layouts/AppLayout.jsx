import { Head, usePage } from '@inertiajs/react';
import { Armchair, Bell, BookOpen, CalendarCheck, DoorOpen, Home, History, ScanLine, Sparkles, Trees, User, LayoutDashboard } from 'lucide-react';
import Shell from '@/Components/Navigation/Shell';

export default function AppLayout({ title, children }) {
  const { unread, auth } = usePage().props;
  const items = [
    { href: route('dashboard'), label: 'Dashboard', icon: Home },
    { href: route('facilities.index', 'library'), label: 'Library Booking', icon: BookOpen },
    { href: route('facilities.index', 'saung'), label: 'Saung Booking', icon: Trees },
    { href: route('facilities.index', 'room'), label: 'Room Booking', icon: DoorOpen },
    { href: route('ai.index'), label: 'AI Assistant', icon: Sparkles },
    { href: route('bookings.index'), label: 'My Booking', icon: CalendarCheck },
    { href: route('bookings.history'), label: 'Booking History', icon: History },
    { href: route('checkin.index'), label: 'QR Check-in', icon: ScanLine },
    { href: route('notifications.index'), label: 'Notifications', icon: Bell, badge: unread },
    { href: route('profile.edit'), label: 'Profile', icon: User },
  ];
  if (auth.user?.role !== 'student') items.push({ href: route('admin.dashboard'), label: 'Panel Admin/Staff', icon: LayoutDashboard });
  return (<><Head title={title} /><Shell title={title} items={items} brand="CampusSpace">{children}</Shell></>);
}
