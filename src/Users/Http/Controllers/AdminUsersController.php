<?php

namespace OptimusCMS\Users\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use OptimusCMS\Users\Http\Resources\AdminUserResource;
use OptimusCMS\Users\Models\AdminUser;

class AdminUsersController extends Controller
{
    /**
     * Display a list of users.
     *
     * @return ResourceCollection
     */
    public function index()
    {
        $users = AdminUser::orderBy('name')->get();

        return AdminUserResource::collection($users);
    }

    /**
     * Create a new user.
     *
     * @param Request $request
     * @return JsonResource
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validateUser($request);

        $user = new AdminUser();

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));

        $user->save();

        return new AdminUserResource($user);
    }

    /**
     * Display the specified user.
     *
     * @param Request $request
     * @param int|null $id
     * @return JsonResource
     */
    public function show(Request $request, $id = null)
    {
        $user = $id
            ? AdminUser::findOrFail($id)
            : $request->user('admin');

        return new AdminUserResource($user);
    }

    /**
     * Update the specified user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResource
     *
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $user = AdminUser::findOrFail($id);

        $this->validateUser($request, $user);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return new AdminUserResource($user);
    }

    /**
     * Delete the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        AdminUser::findOrFail($id)->delete();

        return response()->noContent();
    }

    /**
     * Validate the request.
     *
     * @param Request $request
     * @param AdminUser|null $user
     * @return void
     *
     * @throws ValidationException
     */
    protected function validateUser(Request $request, AdminUser $user = null)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => [
                'required',
                Rule::unique('admin_users')->ignore($user),
            ],
            'password' => ($user ? 'nullable' : 'required').'|min:6',
        ]);
    }
}
