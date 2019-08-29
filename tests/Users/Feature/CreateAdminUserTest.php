<?php

namespace OptimusCMS\Tests\Users\Feature;

use Illuminate\Support\Facades\Hash;
use OptimusCMS\Tests\Users\TestCase;
use OptimusCMS\Users\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateAdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    public function it_can_create_an_admin_user()
    {
        $response = $this->postJson(
            route('admin.api.users.store'),
            $data = $this->validData()
        );

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->expectedJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => $data['username']
                ]
            ]);

        $this->assertNotNull($user = AdminUser::find(
            $response->decodeResponseJson('data.id')
        ));

        $this->assertTrue(Hash::check(
            $data['password'],
            $user->password
        ));
    }

    /** @test */
    public function there_are_required_fields()
    {
        $response = $this->postJson(
            route('admin.api.users.store')
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($requiredFields = [
                'name', 'email', 'username'
            ]);
    }

    /** @test */
    public function the_email_field_must_be_a_valid_email_address()
    {
        $response = $this->postJson(
            route('admin.api.users.store'),
            $data = $this->validData([
                'email' => 'not an email'
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email'
            ]);
    }

    /** @test */
    public function the_password_field_must_be_at_least_6_characters()
    {
        $response = $this->postJson(
            route('admin.api.users.store'),
            $data = $this->validData([
                'password' => 'short'
            ])
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'password'
            ]);
    }

    protected function validData($overrides = [])
    {
        return array_merge([
            'name' => 'New name',
            'email' => 'new@email.com',
            'username' => 'new_username',
            'password' => 'new_password'
        ], $overrides);
    }
}
