<?php

namespace OptimusCMS\Users\Tests\Unit;

use Mockery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OptimusCMS\Tests\Users\TestCase;
use OptimusCMS\Users\Models\AdminUser;
use OptimusCMS\Users\Http\Resources\AdminUserResource;

class AdminUserResourceTest extends TestCase
{
    /**
     * Test that User resource is working, a used Row can be returned as an expected array.
     *
     * @test
     */
    public function it_will_transform_user_data()
    {
        $user = new AdminUser();

        $user->id = 1;
        $user->name = 'Foo Bar';
        $user->email = 'foo@bar.com';
        $user->username = 'foobar';
        $user->password = 'fooBar123';
        $user->unknown_field = true;
        $user->created_at = Carbon::now()->subDay();
        $user->updated_at = Carbon::now();

        $resource = new AdminUserResource($user);
        $request = Mockery::mock(Request::class);

        $this->assertEquals([
            'id' => 1,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'created_at' => (string) $user->created_at,
            'updated_at' => (string) $user->updated_at,
        ], $resource->toArray($request));
    }
}
