<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // `role` TIDAK fillable: mencegah mass-assignment eskalasi hak akses.
    protected $fillable = ['name', 'email', 'password', 'student_id'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isStaff(): bool { return $this->role === 'staff'; }
    public function isOperator(): bool { return in_array($this->role, ['admin', 'staff'], true); }

    public function bookings() { return $this->hasMany(Booking::class); }
    public function campusNotifications() { return $this->hasMany(Notification::class); }
    public function aiConversations() { return $this->hasMany(AIConversation::class); }
}
