<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganization;
use Illuminate\Support\Facades\Auth;

class LandingPageSetting extends Model
{
    protected $fillable = [
        'organization_id',
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
        // Search for school-specific override first, then global
        // Check order: Auth User Org -> Session Org (for guests) -> Global (null)
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');

        $setting = static::where('section', $section)
            ->where('key', $key)
            ->when($organizationId, function ($query, $orgId) {
                $query->where(function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId)
                      ->orWhereNull('organization_id');
                })->orderBy('organization_id', 'DESC');
            }, function ($query) {
                $query->whereNull('organization_id');
            })
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Get extra data by section and key
     */
    public static function getExtra(string $section, string $key, $default = null)
    {
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');

        $setting = static::where('section', $section)
            ->where('key', $key)
            ->when($organizationId, function ($query, $orgId) {
                $query->where(function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId)
                      ->orWhereNull('organization_id');
                })->orderBy('organization_id', 'DESC');
            }, function ($query) {
                $query->whereNull('organization_id');
            })
            ->first();

        return $setting ? $setting->extra : $default;
    }

    /**
     * Set a setting value
     */
    public static function setValue(string $section, string $key, $value, $extra = null)
    {
        $user = Auth::user();
        if (!$user) return null;

        $isGlobalSection = in_array($section, ['about', 'team', 'features']);

        // Only Super Admins can edit global sections (where organization_id is NULL)
        if ($isGlobalSection && !$user->is_super_admin) {
            abort(403, 'Only Super Admins can edit system-wide sections.');
        }

        $orgId = $isGlobalSection ? null : $user->organization_id;

        return static::updateOrCreate(
            ['section' => $section, 'key' => $key, 'organization_id' => $orgId],
            ['value' => $value, 'extra' => $extra]
        );
    }

    /**
     * Get all settings for a section
     */
    public static function getSection(string $section)
    {
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');

        return static::where('section', $section)
            ->when($organizationId, function ($query, $orgId) {
                $query->where(function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId)
                      ->orWhereNull('organization_id');
                })->orderBy('organization_id', 'DESC');
            }, function ($query) {
                $query->whereNull('organization_id');
            })
            ->get()
            ->unique('key') // Keep the first match (school override > global)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get all settings with their extras for a section
     */
    public static function getSectionWithExtras(string $section)
    {
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');

        return static::where('section', $section)
            ->when($organizationId, function ($query, $orgId) {
                $query->where(function ($q) use ($orgId) {
                    $q->where('organization_id', $orgId)
                      ->orWhereNull('organization_id');
                })->orderBy('organization_id', 'DESC');
            }, function ($query) {
                $query->whereNull('organization_id');
            })
            ->get()
            ->unique('key') // Keep school override first
            ->keyBy('key')
            ->map(function ($item) {
                return [
                    'value' => $item->value,
                    'extra' => $item->extra,
                    'image' => $item->image,
                ];
            })
            ->toArray();
    }
}
