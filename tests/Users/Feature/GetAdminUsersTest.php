<?php

namespace OptimusCMS\Users\Tests\Feature;

use OptimusCMS\Users\Tests\TestCase;
use OptimusCMS\Users\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetAdminUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_all_admin_users()
    {
        $users = factory(AdminUser::class, 3)->create();
        $this->signIn($users->first());
        $response = $this->getJson(route('admin.api.users.index'));
        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->expectedJsonStructure()
                ]
            ]);
    }

    /** @test */
    public function it_can_display_a_specific_admin_user()
    {
        $user = factory(AdminUser::class)->create();
        $this->signIn($user);
        $response = $this->getJson(route('admin.api.users.show', [
            'id' => $user->id
        ]));
        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'created_at' => (string) $user->created_at,
                    'updated_at' => (string) $user->updated_at
                ]
            ]);
    }

    /** @test */
    public function it_can_display_the_currently_authenticated_admin_user()
    {
        $user = $this->signIn();
        $response = $this->getJson(route('admin.api.users.authenticated'));
        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => $this->expectedJsonStructure()
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'created_at' => (string) $user->created_at,
                    'updated_at' => (string) $user->updated_at
                ]
            ]);
    }
}
