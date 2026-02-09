<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * FIXED: Custom Eloquent Cast for automatic encryption/decryption of sensitive fields.
 * Used for router passwords and other credentials.
 */
class EncryptedCast implements CastsAttributes
{
    /**
     * Decrypt the value when retrieving from database.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }
        
        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // If decryption fails, it might be plain text (legacy data)
            // Log this for migration purposes but return original value
            \Illuminate\Support\Facades\Log::warning("EncryptedCast: Failed to decrypt field '{$key}' - might be legacy plain text");
            return $value;
        }
    }

    /**
     * Encrypt the value when storing to database.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }
        
        return Crypt::encryptString($value);
    }
}
