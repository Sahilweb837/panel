<?php

namespace App\Traits;

trait HasApiTokens
{
    /**
     * Create a simple personal access token object for API requests.
     */
    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = bin2hex(random_bytes(32));

        return (object) [
            'accessToken' => (object) ['name' => $name, 'token' => hash('sha256', $token)],
            'plainTextToken' => $token,
        ];
    }
}

