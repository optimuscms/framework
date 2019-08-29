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

    /**
     * Test that an Admin user can be created,
     * the response will be json and the Password is hash checked.
     *
     * @test
     */
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

    /**
     * Test that fields are required, the 422 (unprocessed) code is returned,
     * there are also validation errors present.
     *
     * @test
     */
    public function there_are_required_fields()
    {
        $response = $this->postJson(route('admin.api.users.store'));
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($requiredFields = [
                'name', 'email', 'username'
            ]);
        $errors = $response->decodeResponseJson('errors');
        foreach ($requiredFields as $field) {
            $this->assertContains(
                trans('validation.required', ['attribute' => $field]),
                $errors[$field]
            );
        }
    }

    /**
     * Verify that the user must provided an email address.
     * The email string must be in the correct format.
     *
     * @test
     */
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
        $this->assertContains(
            trans('validation.email', ['attribute' => 'email']),
            $response->decodeResponseJson('errors.email')
        );
    }

    /**
     * Verify that the password must be 6 characters or longer,
     * Supply a short password to test
     *
     * There should be validation errors and unprocessed code returned.
     *
     * @test
     */
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
        $this->assertContains(
            trans('validation.min.string', ['attribute' => 'password', 'min' => 6]),
            $response->decodeResponseJson('errors.password')
        );
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
