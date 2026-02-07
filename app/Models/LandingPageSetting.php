<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $fillable = [
        'section',
        'key',
        'value',
        'image',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    /**
     * Get a setting value by section and key
     */
    public static function getValue(string $section, string $key, $default = null)
    {
        $setting = static::where('section', $section)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Get extra data by section and key
     */
    public static function getExtra(string $section, string $key, $default = null)
    {
        $setting = static::where('section', $section)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->extra : $default;
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $section, string $key, $value, $extra = null)
    {
        return static::updateOrCreate(
            ['section' => $section, 'key' => $key],
            ['value' => $value, 'extra' => $extra]
        );
    }

    /**
     * Get all settings for a section
     */
    public static function getSection(string $section)
    {
        return static::where('section', $section)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get all settings with their extras for a section
     */
    public static function getSectionWithExtras(string $section)
    {
        return static::where('section', $section)
            ->get()
            ->keyBy('key')
            ->map(function ($item) {
                return [
                    'value' => $item->value,
                    'extra' => $item->extra,
                ];
            })
            ->toArray();
    }
}
