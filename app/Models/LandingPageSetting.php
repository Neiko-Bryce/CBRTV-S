<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        // On public routes, sessions take precedence. In admin dashboard, Auth user takes precedence.
        ['school_id' => $schoolId, 'organization_id' => $organizationId] = static::getContextIds();

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
        ['school_id' => $schoolId, 'organization_id' => $organizationId] = static::getContextIds();

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
        if (! $user) {
            return null;
        }

        $isGlobalSection = in_array($section, ['about', 'team', 'features']);

        // orgId: only if not a global section
        $orgId = $isGlobalSection ? null : $user->organization_id;

        // schoolId: always set for non-super-admins, or if a super-admin wants to scope to a school
        $schoolId = $user->is_super_admin ? null : $user->school_id;

        // If it's a global section and user is not super_admin, it MUST have a school_id
        if ($isGlobalSection && ! $user->is_super_admin && ! $schoolId) {
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
        ['school_id' => $schoolId, 'organization_id' => $organizationId] = static::getContextIds();

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
        ['school_id' => $schoolId, 'organization_id' => $organizationId] = static::getContextIds();

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

    /**
     * Helper to get context-aware school and organization IDs
     */
    private static function getContextIds()
    {
        $isAdminPath = request()->is('admin*');

        if ($isAdminPath && Auth::check()) {
            return [
                'school_id' => Auth::user()->school_id,
                'organization_id' => Auth::user()->organization_id,
            ];
        }

        return [
            'school_id' => request('school_id') ?: (session('school_id') ?: (Auth::check() ? Auth::user()->school_id : null)),
            'organization_id' => request('organization_id') ?: (session('org_id') ?: (Auth::check() ? Auth::user()->organization_id : null)),
        ];
    }
}
