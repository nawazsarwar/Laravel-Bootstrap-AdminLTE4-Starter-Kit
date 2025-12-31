<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Admin\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Gate;

class UsersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return UserResource::collection(User::with(['roles'])->get());
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->safe()->except('roles'));
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->safe()->except('roles'));
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
