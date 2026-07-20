<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display System Settings Dashboard (Super Admin only).
     */
    public function index()
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Access restricted to Super Admin only.');
        }

        $settings = Setting::getAllGrouped();

        // Default setting fallbacks
        $defaults = [
            'general' => [
                'institute_name' => Setting::get('institute_name', 'NetCoder Learning Institute'),
                'tagline' => Setting::get('tagline', 'Excellence in Education & Training'),
                'contact_email' => Setting::get('contact_email', 'support@netcoder.in'),
                'contact_phone' => Setting::get('contact_phone', '+91 98765 43210'),
                'address' => Setting::get('address', 'Main Campus, IT Park Road, City Center'),
                'logo_url' => Setting::get('logo_url', 'https://www.netcoder.in/images/logo.png'),
                'timezone' => Setting::get('timezone', 'Asia/Kolkata'),
                'date_format' => Setting::get('date_format', 'd M Y'),
            ],
            'financial' => [
                'currency_symbol' => Setting::get('currency_symbol', '₹'),
                'default_registration_fee' => Setting::get('default_registration_fee', '500'),
                'default_prospectus_fee' => Setting::get('default_prospectus_fee', '200'),
                'daily_late_fine' => Setting::get('daily_late_fine', '50'),
                'invoice_prefix' => Setting::get('invoice_prefix', 'ADM-'),
                'default_fee_tenure' => Setting::get('default_fee_tenure', '1 Month'),
            ],
            'wording' => [
                'invoice_terms' => Setting::get('invoice_terms', "1. Fees once paid are non-refundable & non-transferable.\n2. Receipts must be presented for all official inquiries.\n3. Late fee fine applies after the due date."),
                'receipt_footer' => Setting::get('receipt_footer', 'Thank you for choosing NetCoder Learning Institute. This is a computer-generated receipt.'),
                'salary_slip_note' => Setting::get('salary_slip_note', 'Confidential salary slip. Issued for internal staff records.'),
                'welcome_email_text' => Setting::get('welcome_email_text', 'Welcome to NetCoder Fees Manager! Your student account has been created successfully.'),
            ],
            'appearance' => [
                'primary_color' => Setting::get('primary_color', '#ff5532'),
                'default_theme' => Setting::get('default_theme', 'light'),
                'font_family' => Setting::get('font_family', 'Poppins'),
            ],
            'security' => [
                'min_password_length' => Setting::get('min_password_length', '6'),
                'allow_subadmin_password_reset' => Setting::get('allow_subadmin_password_reset', '1'),
                'auto_backup_enabled' => Setting::get('auto_backup_enabled', '1'),
            ],
        ];

        return view('settings.index', compact('settings', 'defaults'));
    }

    /**
     * Update System Settings.
     */
    public function update(Request $request)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Access restricted to Super Admin only.');
        }

        $inputSettings = $request->except(['_token']);

        foreach ($inputSettings as $key => $value) {
            $group = 'general';
            if (str_starts_with($key, 'fin_')) {
                $group = 'financial';
                $realKey = substr($key, 4);
            } elseif (str_starts_with($key, 'word_')) {
                $group = 'wording';
                $realKey = substr($key, 5);
            } elseif (str_starts_with($key, 'app_')) {
                $group = 'appearance';
                $realKey = substr($key, 4);
            } elseif (str_starts_with($key, 'sec_')) {
                $group = 'security';
                $realKey = substr($key, 4);
            } else {
                $realKey = $key;
            }

            Setting::set($realKey, is_array($value) ? json_encode($value) : $value, $group);
        }

        return redirect()->route('settings.index')->with('success', 'System Settings updated successfully!');
    }
}
