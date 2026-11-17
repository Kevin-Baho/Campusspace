<?php

namespace Tests\Feature;

class AccessTest extends CampusSpaceTestCase
{
    public function test_register_and_login_pages_and_flow(): void
    {
        $this->post('/register', ['name' => 'A', 'email' => 'a@x.test', 'password' => 'password-123', 'password_confirmation' => 'password-123']);
        $this->assertAuthenticated();
        $this->assertSame('student', \App\Models\User::first()->role); // role tidak bisa di-set saat register
    }

    public function test_register_cannot_set_role(): void
    {
        $this->post('/register', ['name' => 'A', 'email' => 'a@x.test', 'password' => 'password-123', 'password_confirmation' => 'password-123', 'role' => 'admin']);
        $this->assertSame('student', \App\Models\User::first()->role);
    }

    public function test_guest_redirected(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_student_blocked_from_admin(): void
    {
        $s = $this->user('student');
        foreach (['/admin', '/admin/users', '/admin/facilities', '/admin/bookings', '/admin/settings'] as $p) {
            $this->actingAs($s)->get($p)->assertForbidden();
        }
    }

    public function test_staff_operational_only(): void
    {
        $st = $this->user('staff');
        $this->actingAs($st)->get('/admin')->assertOk();
        $this->actingAs($st)->get('/admin/bookings')->assertOk();
        $this->actingAs($st)->get('/admin/qr')->assertOk();
        $this->actingAs($st)->get('/admin/users')->assertForbidden();
        $this->actingAs($st)->get('/admin/settings')->assertForbidden();
        $this->actingAs($st)->patch('/admin/users/' . $st->id, ['role' => 'admin'])->assertForbidden();
    }

    public function test_admin_can_access_and_change_role(): void
    {
        $a = $this->user('admin'); $s = $this->user('student');
        $this->actingAs($a)->get('/admin/users')->assertOk();
        $this->actingAs($a)->patch("/admin/users/{$s->id}", ['role' => 'staff']);
        $this->assertSame('staff', $s->fresh()->role);
    }
}
