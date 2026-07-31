<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get the logo URL, resolving relative paths to assets.
     */
    public static function getLogoUrl(): string
    {
        $logo = static::get('logo_url', 'images/logo.png');
        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }
        return asset($logo);
    }

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            if (!Schema::hasTable('settings')) {
                return $default;
            }
            return Cache::remember("sys_setting_{$key}", 3600, function () use ($key, $default) {
                $setting = static::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set or update a setting by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): static|null
    {
        try {
            if (!Schema::hasTable('settings')) {
                return null;
            }
            $setting = static::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );

            Cache::forget("sys_setting_{$key}");
            Cache::forget('sys_settings_all');

            return $setting;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get all settings grouped by group name.
     */
    public static function getAllGrouped(): array
    {
        try {
            if (!Schema::hasTable('settings')) {
                return [];
            }
            return Cache::remember('sys_settings_all', 3600, function () {
                $all = static::all();
                $grouped = [];
                foreach ($all as $setting) {
                    $grouped[$setting->group][$setting->key] = $setting->value;
                }
                return $grouped;
            });
        } catch (\Throwable $e) {
            return [];
        }
    }
}
