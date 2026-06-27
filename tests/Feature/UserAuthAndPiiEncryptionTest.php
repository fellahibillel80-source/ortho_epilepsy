<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class UserAuthAndPiiEncryptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration and PII database encryption.
     */
    public function test_user_registration_stores_encrypted_pii_in_database()
    {
        $plainName = 'سليم الجزائري';
        $plainEmail = 'salim@example.com';
        $plainPassword = 'password123';
        $plainEmergencyContact = '0666112233';

        // 1. Send register API request
        $response = $this->postJson('/api/register', [
            'name' => $plainName,
            'email' => $plainEmail,
            'password' => $plainPassword,
            'emergency_contact' => $plainEmergencyContact,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'emergency_contact',
                'role',
            ]
        ]);

        // Verify the response contains decrypted data
        $response->assertJsonPath('user.name', $plainName);
        $response->assertJsonPath('user.emergency_contact', $plainEmergencyContact);

        // 2. Fetch directly from the DB facade (bypassing Eloquent decryption)
        $rawUser = DB::table('users')->where('email', $plainEmail)->first();

        $this->assertNotNull($rawUser);
        
        // The raw database columns must NOT equal the plain-text values due to encryption
        $this->assertNotEquals($plainName, $rawUser->name);
        $this->assertNotEquals($plainEmergencyContact, $rawUser->emergency_contact);

        // Verify they are encrypted strings (e.g., ciphertext containing payload)
        $this->assertStringStartsWith('eyJpdiI6', $rawUser->name); // standard Laravel encryption starts with base64 encoded json containing iv
        $this->assertStringStartsWith('eyJpdiI6', $rawUser->emergency_contact);

        // 3. Fetch via Eloquent (should automatically decrypt)
        $eloquentUser = User::where('email', $plainEmail)->first();
        
        $this->assertEquals($plainName, $eloquentUser->name);
        $this->assertEquals($plainEmergencyContact, $eloquentUser->emergency_contact);
    }
}
