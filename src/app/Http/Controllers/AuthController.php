<?php

namespace Laurel\Kanban\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Laurel\Kanban\App\Http\Resources\UserResource;
use Laurel\Kanban\App\Http\Requests\Auth\Login;
use Laurel\Kanban\App\Http\Requests\Auth\Register;
use Laurel\Kanban\App\Kanban;
use Laurel\Kanban\App\Models\Card;

class AuthController extends Controller
{
    public function init(Request $request)
    {
        if (Auth::user()) {
            return response([
                'data' => new UserResource(Auth::user())
            ]);
        } else {
            return response(['errors' => 'Unauthorized'], 401);
        }
    }

    public function login(Login $request)
    {
        try {
            $this->logout($request);

            if (Auth::guard('web')->attempt([
                'email' => $request->validated()['email'],
                'password' => $request->validated()['password']
            ], false, false)) {
                return response([
                    'data' => new UserResource(Auth::user())
                ]);
            } else {
                return response([
                    'errors' => ['Invalid credentials...']
                ]);
            }
        } catch (\Exception $e) {
            return response([
                'errors' => ['Couldnt login you. Try again later...']
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();
            return response([
                'data' => true
            ]);
        } catch (\Exception $e) {
            return response([
                'errors' => ['Could not logout you. Try again later...']
            ]);
        }
    }

    public function register(Register $request)
    {
        try {
            $userClass = config('laurel_kanban.user_class');
            $credentials = $request->validated();
            $credentials['password'] = bcrypt($credentials['password']);
            $user = new $userClass;
            $user->fill($credentials);
            $user->save();
            Auth::guard('web')->login($user);

            return response([
                'data' => new UserResource($user)
            ]);
        } catch (\Exception $e) {
            return response([
                'errors' => ['Couldnt create user. Try again later...']
            ]);
        }
    }
}
