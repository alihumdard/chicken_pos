<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\RateFormula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings configuration page.
     * Fetches general settings and rate formulas data.
     */
    public function index()
    {
        // Fetch current general settings (ID 1)
        $settings = Setting::getGlobalSettings();

        // Fetch all rate formulas (needed for the formula configuration section on the right)
        $rateFormulas = RateFormula::all()->keyBy('rate_key');

        // Pass settings data and formulas to the view
        // Note: The sidebar relies on a View Composer to globally inject settings, 
        // but the settings page needs the data explicitly.
        return view('pages.settings', compact('settings', 'rateFormulas'));
    }

    /**
     * Store or update the general configuration settings (Shop Name, Logo, etc.).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'shop_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        $settings = Setting::getGlobalSettings();
        
        // 1. Handle File Upload
        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            
            // Delete old logo if it exists
            // We convert the 'storage/' URL path back to the 'public/' storage path for deletion.
            if ($settings->logo_url && Storage::exists(str_replace('storage/', 'public/', $settings->logo_url))) {
                Storage::delete(str_replace('storage/', 'public/', $settings->logo_url));
            }
            
            // Store the new file in the 'public/logos' directory
            $path = $file->store('public/logos');
            
            // Save the public path in the format expected by asset() helper (e.g., 'storage/logos/...')
            $settings->logo_url = str_replace('public/', 'storage/', $path);
        }

        // 2. Update scalar fields
        // Use the validated data, falling back to existing data if input is missing/null
        $settings->shop_name = $validatedData['shop_name'] ?? $settings->shop_name;
        $settings->address = $validatedData['address'] ?? $settings->address;
        $settings->phone_number = $validatedData['phone_number'] ?? $settings->phone_number;
        
        // Ensure we save/update the first record
        $settings->id = 1;
        $settings->save();

        // This redirection triggers a full page reload, which forces the View Composer
        // to re-read the latest data (including the new shop_name and logo_url) 
        // from the database and update the sidebar globally.
        return back()->with('success', 'General settings updated successfully!');
    }
}