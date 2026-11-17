<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityAmenity;
use App\Models\FacilityUnit;
use App\Models\User;
use App\Services\QRCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder DEMO, aman dijalankan ulang (firstOrCreate). Jangan dipakai di produksi tanpa mengganti password.
 * Tidak menghapus data apa pun.
 */
class CampusSpaceSeeder extends Seeder
{
    public function run(): void
    {
        $qr = app(QRCodeService::class);
        $pw = env('SEED_PASSWORD', 'ChangeMe!12345');

        foreach ([['Admin', 'admin@campusspace.test', 'admin'], ['Staff', 'staff@campusspace.test', 'staff'], ['Mahasiswa Demo', 'student@campusspace.test', 'student']] as [$n, $e, $r]) {
            $u = User::firstOrCreate(['email' => $e], ['name' => $n, 'password' => Hash::make($pw)]);
            $u->forceFill(['role' => $r, 'email_verified_at' => now()])->save();
        }

        $lib = Facility::firstOrCreate(['name' => 'Perpustakaan Pusat', 'category' => 'library'], ['location' => 'Gedung Perpustakaan', 'floor' => '1', 'is_active' => true]);
        for ($i = 1; $i <= 10; $i++) $this->unit($lib, sprintf('LIB-%02d', $i), "Kursi $i", 1, '1', $qr);

        $saung = Facility::firstOrCreate(['name' => 'Saung Taman', 'category' => 'saung'], ['location' => 'Taman Kampus', 'capacity' => 8, 'is_active' => true]);
        foreach (['Colokan listrik', 'Wi-Fi'] as $a) FacilityAmenity::firstOrCreate(['facility_id' => $saung->id, 'name' => $a]);
        for ($i = 1; $i <= 3; $i++) $this->unit($saung, "SAUNG-$i", "Saung $i", 8, null, $qr);

        $room = Facility::firstOrCreate(['name' => 'Ruang Diskusi', 'category' => 'room'], ['location' => 'Gedung A', 'floor' => '2', 'is_active' => true]);
        foreach (['Proyektor', 'Whiteboard', 'AC'] as $a) FacilityAmenity::firstOrCreate(['facility_id' => $room->id, 'name' => $a]);
        foreach ([['R-201', 6], ['R-202', 10], ['R-203', 20]] as [$c, $cap]) $this->unit($room, $c, "Ruang $c", $cap, '2', $qr);
    }

    private function unit(Facility $f, string $code, string $name, int $cap, ?string $floor, QRCodeService $qr): void
    {
        if (FacilityUnit::where('code', $code)->exists()) return;
        $u = new FacilityUnit(['facility_id' => $f->id, 'code' => $code, 'name' => $name, 'capacity' => $cap, 'floor' => $floor, 'status' => 'available', 'is_active' => true]);
        $u->forceFill(['qr_token' => $qr->newToken()])->save();
    }
}
