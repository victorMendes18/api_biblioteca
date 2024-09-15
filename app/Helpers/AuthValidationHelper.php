<?php

use Illuminate\Http\Exceptions\HttpResponseException;


if (!function_exists('checkUserLoggedVerified')) {
    function checkUserLoggedVerified($userLogged)
    {
        if (!$userLogged->email_verified_at){
            throw new HttpResponseException(
                response()->json([
                    'message' => 'User is not verified.',
                ], 401)
            );
        }

        return true;
    }
}

if (!function_exists('checkUserLoggedAdm')) {
    function checkUserLoggedAdm($userLogged)
    {
        if ($userLogged->type == 'librarian'){
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Librarian user does not have access.',
                ], 401)
            );
        }

        return true;
    }
}
