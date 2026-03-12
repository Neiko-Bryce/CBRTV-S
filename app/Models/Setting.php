<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * Check if Maintenance Mode is enabled globally.
     */
    public static function isMaintenanceModeEnabled(): bool
    {
        try {
            $setting = self::where('key', 'maintenance_mode')->first();
            return $setting && $setting->value === 'true';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Enable or disable Maintenance Mode.
     */
    public static function setMaintenanceMode(bool $enabled): void
    {
        self::updateOrCreate(
            ['key' => 'maintenance_mode'],
            [
                'value' => $enabled ? 'true' : 'false',
                'description' => 'Global Emergency Maintenance Mode toggle (true/false)'
            ]
        );
    }
}
