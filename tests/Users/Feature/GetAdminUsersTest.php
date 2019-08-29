<?php

namespace OptimusCMS\Tests\Users\Feature;

use OptimusCMS\Tests\Users\TestCase;
use OptimusCMS\Users\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetAdminUsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create and then retrieve a list of 3 users, verify the response is in the correct format.
     *
     * @test
     */
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

    /**
     * Create a user, sign them in and retrieve the row that matches the users ID.
     *
     * @test
     */
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

    /**
     * Sign in a user and retrieve the DB row that is related to that user only.
     *
     * @test
     */
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
