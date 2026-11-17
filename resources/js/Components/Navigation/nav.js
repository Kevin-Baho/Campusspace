import { Armchair, Bell, Bot, CalendarCheck, History, LayoutDashboard, TreePine, DoorOpen, User, Users, Building2, Boxes, QrCode, Star, BarChart3, Settings, ScanLine } from 'lucide-react';

export const studentNav = [
    { name: 'Dashboard', route: 'dashboard', icon: LayoutDashboard, active: 'dashboard' },
    { name: 'Library Booking', route: 'facilities.index', params: 'library', icon: Armchair, active: 'facilities.index', match: 'library' },
    { name: 'Saung Booking', route: 'facilities.index', params: 'saung', icon: TreePine, active: 'facilities.index', match: 'saung' },
    { name: 'Room Booking', route: 'facilities.index', params: 'room', icon: DoorOpen, active: 'facilities.index', match: 'room' },
    { name: 'AI Assistant', route: 'ai.index', icon: Bot, active: 'ai.*' },
    { name: 'My Booking', route: 'bookings.index', icon: CalendarCheck, active: 'bookings.index' },
    { name: 'Booking History', route: 'bookings.history', icon: History, active: 'bookings.history' },
    { name: 'Check-in QR', route: 'checkin.index', icon: ScanLine, active: 'checkin.*' },
    { name: 'Notifications', route: 'notifications.index', icon: Bell, active: 'notifications.*', badge: true },
    { name: 'Profile', route: 'profile.edit', icon: User, active: 'profile.*' },
];

export const adminNav = [
    { name: 'Dashboard', route: 'admin.dashboard', icon: LayoutDashboard, active: 'admin.dashboard' },
    { name: 'Users', route: 'admin.users.index', icon: Users, active: 'admin.users.*', adminOnly: true },
    { name: 'Facilities', route: 'admin.facilities.index', icon: Building2, active: 'admin.facilities.*', adminOnly: true },
    { name: 'Facility Units', route: 'admin.units.index', icon: Boxes, active: 'admin.units.*', adminOnly: true },
    { name: 'Bookings', route: 'admin.bookings.index', icon: CalendarCheck, active: 'admin.bookings.*' },
    { name: 'QR Management', route: 'admin.qr.index', icon: QrCode, active: 'admin.qr.*' },
    { name: 'Reviews', route: 'admin.reviews.index', icon: Star, active: 'admin.reviews.*' },
    { name: 'Reports', route: 'admin.reports.index', icon: BarChart3, active: 'admin.reports.*', adminOnly: true },
    { name: 'System Settings', route: 'admin.settings.index', icon: Settings, active: 'admin.settings.*', adminOnly: true },
];
