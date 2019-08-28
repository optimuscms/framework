<?php

namespace OptimusCMS\Tests\Users\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use OptimusCMS\Tests\Users\TestCase;
use OptimusCMS\Users\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateAdminUsersTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(AdminUser::class)->create([
            'name' => 'Old name',
            'email' => 'old@email.com',
            'username' => 'old_username',
            'password' => bcrypt('old_password')
        ]);
        $this->signIn($this->user);
    }

    /** @test */
    public function it_can_update_an_admin_user()
    {
        $response = $this->patchJson(route('admin.api.users.update', [
            'id' => $this->user->id
        ]), $newData = $this->validData());
        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'name' => $newData['name'],
                    'email' => $newData['email'],
                    'username' => $newData['username']
                ]
            ]);
        $this->assertTrue(Hash::check(
            $newData['password'],
            $this->user->fresh()->password
        ));
    }

    /** @test */
    public function it_will_not_update_passwords_unless_the_field_is_present()
    {
        $response = $this->patchJson(route('admin.api.users.update', [
            'id' => $this->user->id
        ]), $newData = Arr::except(
            $this->validData(), 'password'
        ));
        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'name' => $newData['name'],
                    'email' => $newData['email'],
                    'username' => $newData['username']
                ]
            ]);
        $this->assertTrue(Hash::check(
            'old_password',
            $this->user->password
        ));
    }

    /** @test */
    public function there_are_required_fields()
    {
        $response = $this->patchJson(route('admin.api.users.update', [
            'id' => $this->user->id
        ]));
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

    /** @test */
    public function the_email_field_must_be_a_valid_email_address()
    {
        $response = $this->patchJson(route('admin.api.users.update', [
            'id' => $this->user->id
        ]), $newData = $this->validData([
            'email' => 'not an email'
        ]));
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

    /** @test */
    public function the_password_field_must_be_at_least_6_characters()
    {
        $response = $this->patchJson(route('admin.api.users.update', [
            'id' => $this->user->id
        ]), $newData = $this->validData([
            'password' => 'short'
        ]));
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
