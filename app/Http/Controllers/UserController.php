<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 *
 */
class UserController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Bad request!', $validator->messages()->toArray());
        }

        $error = false;

        /** @var User $user */
        $user = User::where('email', $request->get('email'))->first();
        if (!$user) {
            $error = true;
        } else {
            if (!Hash::check($request->get('password'), $user->password)) {
                $error = true;
            }
        }

        if ($error) {
            return $this->sendError('Bad credentials!');
        }

        $token = $user->createToken('Practica');

        return $this->sendResponse([
            'token' => $token->plainTextToken,
            'user' => $user->toArray()
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $register = [
            'name' => 'unique:users|required',
            'email'    => 'unique:users|required',
            'password' => 'required',
        ];
    
        $input     = $request->only('name', 'email','password');
        $validator = Validator::make($input, $register);
    
        if ($validator->fails()) {
            return $this->sendError('Bad request!', $validator->messages()->toArray());
        }

        $name = $request->name;
        $email    = $request->email;
        $password = $request->password;
        $user     = User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);

        $token = $user->createToken('dsa');

        return $this->sendResponse([
            'token' => $token->plainTextToken,
            'user' => $user->toArray()
        ]);

        
        
    }
}
