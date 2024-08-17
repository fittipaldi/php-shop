<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;

    protected $table = 'api_tokens';

    public static function isTokenValid(string $token): bool
    {
        $apiToken = self::where('token', '=', $token)
            ->where('status', '=', '1')
            ->first();
        if (!$apiToken) {
            return false;
        }
        if ($apiToken->token != $token) {
            return false;
        }
        return true;
    }

}
