<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserPhoneAndRememberTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_phone_and_remember_token_columns(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'phone'));
        $this->assertTrue(Schema::hasColumn('users', 'remember_token'));
    }

    public function test_user_can_be_created_with_phone_via_mass_assignment(): void
    {
        $user = User::create([
            'name' => 'WhatsApp User',
            'email' => 'whatsapp@example.com',
            'phone' => '+1234567890',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '+1234567890',
        ]);
        $this->assertEquals('+1234567890', $user->fresh()->phone);
    }

    public function test_phone_is_nullable(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => 'password123',
        ]);

        $this->assertNull($user->fresh()->phone);
    }
}
