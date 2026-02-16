<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganization;
use Illuminate\Support\Facades\Auth;

class LandingPageSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'school_id',
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
        // Check order: Auth User Org -> Session Org -> Auth School -> Session School -> Global (null)
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');
        $schoolId = Auth::check() ? Auth::user()->school_id : session('school_id');

        $setting = static::where('section', $section)
            ->where('key', $key)
            ->where(function ($query) use ($organizationId, $schoolId) {
                $query->whereNull('organization_id')
                      ->whereNull('school_id');
                
                if ($schoolId) {
                    $query->orWhere(function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId)->whereNull('organization_id');
                    });
                }
                
                if ($organizationId) {
                    $query->orWhere('organization_id', $organizationId);
                }
            })
            ->orderByRaw('organization_id DESC, school_id DESC')
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Get extra data by section and key
     */
    public static function getExtra(string $section, string $key, $default = null)
    {
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');
        $schoolId = Auth::check() ? Auth::user()->school_id : session('school_id');

        $setting = static::where('section', $section)
            ->where('key', $key)
            ->where(function ($query) use ($organizationId, $schoolId) {
                $query->whereNull('organization_id')
                      ->whereNull('school_id');
                
                if ($schoolId) {
                    $query->orWhere(function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId)->whereNull('organization_id');
                    });
                }
                
                if ($organizationId) {
                    $query->orWhere('organization_id', $organizationId);
                }
            })
            ->orderByRaw('organization_id DESC, school_id DESC')
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
        
        // orgId: only if not a global section
        $orgId = $isGlobalSection ? null : $user->organization_id;
        
        // schoolId: always set for non-super-admins, or if a super-admin wants to scope to a school
        $schoolId = $user->is_super_admin ? null : $user->school_id;

        // If it's a global section and user is not super_admin, it MUST have a school_id
        if ($isGlobalSection && !$user->is_super_admin && !$schoolId) {
            abort(403, 'Unauthorized to edit global settings without school scope.');
        }

        return static::updateOrCreate(
            ['section' => $section, 'key' => $key, 'organization_id' => $orgId, 'school_id' => $schoolId],
            ['value' => $value, 'extra' => $extra]
        );
    }

    /**
     * Get all settings for a section
     */
    public static function getSection(string $section)
    {
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');
        $schoolId = Auth::check() ? Auth::user()->school_id : session('school_id');

        return static::where('section', $section)
            ->where(function ($query) use ($organizationId, $schoolId) {
                $query->whereNull('organization_id')
                      ->whereNull('school_id');
                
                if ($schoolId) {
                    $query->orWhere(function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId)->whereNull('organization_id');
                    });
                }
                
                if ($organizationId) {
                    $query->orWhere('organization_id', $organizationId);
                }
            })
            ->orderByRaw('organization_id DESC, school_id DESC')
            ->get()
            ->unique('key')
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get all settings with their extras for a section
     */
    public static function getSectionWithExtras(string $section)
    {
        $organizationId = Auth::check() ? Auth::user()->organization_id : session('org_id');
        $schoolId = Auth::check() ? Auth::user()->school_id : session('school_id');

        return static::where('section', $section)
            ->where(function ($query) use ($organizationId, $schoolId) {
                $query->whereNull('organization_id')
                      ->whereNull('school_id');
                
                if ($schoolId) {
                    $query->orWhere(function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId)->whereNull('organization_id');
                    });
                }
                
                if ($organizationId) {
                    $query->orWhere('organization_id', $organizationId);
                }
            })
            ->orderByRaw('organization_id DESC, school_id DESC')
            ->get()
            ->unique('key')
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
