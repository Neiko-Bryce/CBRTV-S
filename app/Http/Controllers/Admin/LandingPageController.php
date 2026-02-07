<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LandingPageController extends Controller
{
    /**
     * Display the landing page management form
     */
    public function index()
    {
        $aboutSettings = LandingPageSetting::getSectionWithExtras('about');
        $featuresSettings = LandingPageSetting::getSectionWithExtras('features');

        return view('admin.landing-page.index', compact('aboutSettings', 'featuresSettings'));
    }

    /**
     * Update landing page settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'about_title' => 'nullable|string|max:255',
            'about_subtitle' => 'nullable|string|max:255',
            'about_description' => 'nullable|string',
            'about_main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'about_benefits' => 'nullable|array',
            'about_benefits.*' => 'nullable|string|max:255',
            'team_section_title' => 'nullable|string|max:255',
            'team_section_subtitle' => 'nullable|string|max:255',
            'team_members' => 'nullable|array',
            'team_members.*.name' => 'nullable|string|max:255',
            'team_members.*.role' => 'nullable|string|max:255',
            'team_members.*.bio' => 'nullable|string',
            'team_members.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'features_title' => 'nullable|string|max:255',
            'features_subtitle' => 'nullable|string|max:255',
            'features_description' => 'nullable|string',
            'features_items' => 'nullable|array',
            'features_items.*.title' => 'nullable|string|max:255',
            'features_items.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update About section settings
        LandingPageSetting::setValue('about', 'title', $request->about_title);
        LandingPageSetting::setValue('about', 'subtitle', $request->about_subtitle);
        LandingPageSetting::setValue('about', 'description', $request->about_description);
        LandingPageSetting::setValue('about', 'benefits', null, $request->about_benefits);

        // Team section titles
        LandingPageSetting::setValue('about', 'team_section_title', $request->team_section_title);
        LandingPageSetting::setValue('about', 'team_section_subtitle', $request->team_section_subtitle);

        // Handle About main image upload
        if ($request->hasFile('about_main_image')) {
            $image = $request->file('about_main_image');
            $imageName = 'about_main_'.time().'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('landing-page/about', $imageName, 'public');

            // Delete old image if exists
            $existingImage = LandingPageSetting::getValue('about', 'main_image');
            if ($existingImage) {
                Storage::disk('public')->delete($existingImage);
            }

            LandingPageSetting::setValue('about', 'main_image', $imagePath);
        }

        // Handle team members with image uploads
        if ($request->has('team_members')) {
            $teamMembers = [];
            foreach ($request->team_members as $index => $member) {
                $imagePath = null;

                // Check if there's a new image upload
                if ($request->hasFile("team_members.{$index}.image")) {
                    $image = $request->file("team_members.{$index}.image");
                    $imageName = 'team_'.time().'_'.$index.'.'.$image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('landing-page/team', $imageName, 'public');

                    // Delete old image if exists
                    if (isset($member['existing_image'])) {
                        Storage::disk('public')->delete($member['existing_image']);
                    }
                } elseif (isset($member['existing_image'])) {
                    // Keep existing image
                    $imagePath = $member['existing_image'];
                }

                $teamMembers[] = [
                    'name' => $member['name'] ?? '',
                    'role' => $member['role'] ?? '',
                    'bio' => $member['bio'] ?? '',
                    'image' => $imagePath,
                ];
            }
            LandingPageSetting::setValue('about', 'team_members', null, $teamMembers);
        } else {
            // Clear team members if none provided
            LandingPageSetting::setValue('about', 'team_members', null, []);
        }

        // Update Features section settings
        LandingPageSetting::setValue('features', 'title', $request->features_title);
        LandingPageSetting::setValue('features', 'subtitle', $request->features_subtitle);
        LandingPageSetting::setValue('features', 'description', $request->features_description);
        LandingPageSetting::setValue('features', 'items', null, $request->features_items);

        return redirect()
            ->route('admin.landing-page.index')
            ->with('success', 'Landing page settings updated successfully.');
    }

    /**
     * Reset all settings to default
     */
    public function reset()
    {
        // Delete About main image
        $aboutMainImage = LandingPageSetting::getValue('about', 'main_image');
        if ($aboutMainImage) {
            Storage::disk('public')->delete($aboutMainImage);
        }

        // Delete uploaded team member images
        $teamMembersSettings = LandingPageSetting::where('section', 'about')
            ->where('key', 'team_members')
            ->first();

        if ($teamMembersSettings && $teamMembersSettings->extra) {
            foreach ($teamMembersSettings->extra as $member) {
                if (isset($member['image'])) {
                    Storage::disk('public')->delete($member['image']);
                }
            }
        }

        LandingPageSetting::whereIn('section', ['about', 'features'])->delete();

        return redirect()
            ->route('admin.landing-page.index')
            ->with('success', 'Landing page settings reset to defaults.');
    }
}
