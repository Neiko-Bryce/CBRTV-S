<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * @deprecated Use per-school lockdown: {@see School::maintenance_mode} and {@see School::maintenanceEnabledForId()}.
     */
    public static function isMaintenanceModeEnabled(): bool
    {
        return false;
    }

    /**
     * @deprecated Use per-school lockdown on the schools table.
     */
    public static function setMaintenanceMode(bool $enabled): void
    {
        self::updateOrCreate(
            ['key' => 'maintenance_mode'],
            [
                'value' => $enabled ? 'true' : 'false',
                'description' => 'Legacy global key (unused); maintenance is per school.',
            ]
        );
    }
}
