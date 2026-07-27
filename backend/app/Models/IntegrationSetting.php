<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property bool $is_encrypted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['key', 'value', 'is_encrypted'])]
class IntegrationSetting extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    /**
     * Keys that must be encrypted when stored.
     *
     * @var array<int, string>
     */
    protected static array $encryptedKeys = [
        'telegram_bot_token',
    ];

    /**
     * Get a setting value by key.
     * Automatically decrypts if it was stored as encrypted.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting || $setting->value === null) {
            return $default;
        }

        if ($setting->is_encrypted) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Exception) {
                return $default;
            }
        }

        return $setting->value;
    }

    /**
     * Set a setting value by key.
     * Automatically encrypts keys that are in the $encryptedKeys list.
     */
    public static function setValue(string $key, mixed $value): static
    {
        $shouldEncrypt = in_array($key, static::$encryptedKeys, true);

        $storedValue = $value;
        if ($shouldEncrypt && $value !== null && $value !== '') {
            $storedValue = Crypt::encryptString((string) $value);
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => ($value === null || $value === '') ? null : $storedValue,
                'is_encrypted' => $shouldEncrypt,
            ]
        );
    }

    /**
     * Get multiple setting values at once.
     *
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public static function getMany(array $keys): array
    {
        $settings = static::whereIn('key', $keys)->get()->keyBy('key');
        $result = [];

        foreach ($keys as $key) {
            $setting = $settings->get($key);

            if (! $setting || $setting->value === null) {
                $result[$key] = null;

                continue;
            }

            if ($setting->is_encrypted) {
                try {
                    $result[$key] = Crypt::decryptString($setting->value);
                } catch (\Exception) {
                    $result[$key] = null;
                }

                continue;
            }

            $result[$key] = $setting->value;
        }

        return $result;
    }

    /**
     * Set multiple setting values at once.
     *
     * @param  array<string, mixed>  $settings
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::setValue($key, $value);
        }
    }
}
