@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'Super Admin System Settings')

@section('content')
<style>
    .settings-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    html[data-theme="dark"] .settings-card {
        background: rgba(31, 41, 55, 0.45);
        backdrop-filter: blur(12px);
    }
    .settings-nav .nav-link {
        color: var(--muted);
        font-weight: 600;
        padding: 14px 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
        margin-bottom: 4px;
    }
    .settings-nav .nav-link.active {
        background: rgba(255, 85, 50, 0.1);
        color: var(--first-color);
        font-weight: 700;
    }
    .form-label-custom {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .form-control-custom {
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid var(--input-border);
        background: var(--surface);
        color: var(--text);
        font-family: var(--font-sans);
        width: 100%;
        transition: all 0.2s ease;
    }
    .form-control-custom:focus {
        border-color: var(--first-color);
        box-shadow: 0 0 0 3px var(--input-focus);
        outline: none;
    }
    .section-header {
        border-bottom: 1px solid var(--border);
        padding-bottom: 12px;
        margin-bottom: 24px;
    }
</style>

<div class="settings-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Settings Sidebar Navigation -->
        <div class="col-12 col-lg-3">
            <div class="settings-card p-3">
                <div class="d-flex align-items-center gap-3 p-3 mb-2 border-bottom">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 85, 50, 0.12); color: var(--first-color); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark-title">Control Center</h6>
                        <small class="text-muted">Super Admin Settings</small>
                    </div>
                </div>

                <div class="nav flex-column nav-pills settings-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab">
                        <i class="fas fa-building" style="width: 20px;"></i> General Profile
                    </button>
                    <button class="nav-link" id="v-pills-financial-tab" data-bs-toggle="pill" data-bs-target="#v-pills-financial" type="button" role="tab">
                        <i class="fas fa-coins" style="width: 20px;"></i> Financial & Fee Rules
                    </button>
                    <button class="nav-link" id="v-pills-wording-tab" data-bs-toggle="pill" data-bs-target="#v-pills-wording" type="button" role="tab">
                        <i class="fas fa-file-signature" style="width: 20px;"></i> Custom Wording & Terms
                    </button>
                    <button class="nav-link" id="v-pills-appearance-tab" data-bs-toggle="pill" data-bs-target="#v-pills-appearance" type="button" role="tab">
                        <i class="fas fa-palette" style="width: 20px;"></i> Appearance & Theme
                    </button>
                    <button class="nav-link" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab">
                        <i class="fas fa-shield-alt" style="width: 20px;"></i> Security & Tools
                    </button>
                </div>
            </div>
        </div>

        <!-- Settings Content Tabs -->
        <div class="col-12 col-lg-9">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="settings-card p-4 p-md-5">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- TAB 1: GENERAL PROFILE -->
                        <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel">
                            <div class="section-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark-title"><i class="fas fa-building text-first me-2"></i>Institute & Company Profile</h5>
                                    <p class="text-muted small mb-0">General details displayed across student receipts, invoices, and system branding.</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Institute / Organization Name</label>
                                    <input type="text" name="institute_name" value="{{ $defaults['general']['institute_name'] }}" class="form-control-custom" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Tagline / Subtitle</label>
                                    <input type="text" name="tagline" value="{{ $defaults['general']['tagline'] }}" class="form-control-custom" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Contact Email</label>
                                    <input type="email" name="contact_email" value="{{ $defaults['general']['contact_email'] }}" class="form-control-custom" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Contact Phone</label>
                                    <input type="text" name="contact_phone" value="{{ $defaults['general']['contact_phone'] }}" class="form-control-custom" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label-custom">Official Address</label>
                                    <textarea name="address" rows="2" class="form-control-custom">{{ $defaults['general']['address'] }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Company Logo (Upload or URL)</label>
                                    <input type="text" name="logo_url" value="{{ $defaults['general']['logo_url'] }}" class="form-control-custom mb-2" placeholder="Logo image URL (optional)" />
                                    <input type="file" name="logo_file" class="form-control-custom" accept="image/*" />
                                    @if($defaults['general']['logo_url'])
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <span class="small text-muted">Current Logo:</span>
                                            <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo Preview" style="max-height: 40px; max-width: 150px; object-fit: contain;" class="border p-1 rounded bg-light">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Timezone</label>
                                    <select name="timezone" class="form-control-custom">
                                        <option value="Asia/Kolkata" {{ $defaults['general']['timezone'] == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                        <option value="UTC" {{ $defaults['general']['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: FINANCIAL & FEE RULES -->
                        <div class="tab-pane fade" id="v-pills-financial" role="tabpanel">
                            <div class="section-header">
                                <h5 class="fw-bold mb-1 text-dark-title"><i class="fas fa-coins text-first me-2"></i>Financial & Fee Rules</h5>
                                <p class="text-muted small mb-0">Configure default fees, currency symbol, invoice prefix, and late fine amounts.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label-custom">Currency Symbol</label>
                                    <input type="text" name="fin_currency_symbol" value="{{ $defaults['financial']['currency_symbol'] }}" class="form-control-custom" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Default Registration Fee (₹)</label>
                                    <input type="number" step="0.01" name="fin_default_registration_fee" value="{{ $defaults['financial']['default_registration_fee'] }}" class="form-control-custom" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Default Prospectus Fee (₹)</label>
                                    <input type="number" step="0.01" name="fin_default_prospectus_fee" value="{{ $defaults['financial']['default_prospectus_fee'] }}" class="form-control-custom" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Daily Late Fine Rate (₹/day)</label>
                                    <input type="number" step="0.01" name="fin_daily_late_fine" value="{{ $defaults['financial']['daily_late_fine'] }}" class="form-control-custom" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Invoice Number Prefix</label>
                                    <input type="text" name="fin_invoice_prefix" value="{{ $defaults['financial']['invoice_prefix'] }}" class="form-control-custom" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">Default Fee Tenure</label>
                                    <select name="fin_default_fee_tenure" class="form-control-custom">
                                        <option value="1 Month" {{ $defaults['financial']['default_fee_tenure'] == '1 Month' ? 'selected' : '' }}>1 Month</option>
                                        <option value="3 Months" {{ $defaults['financial']['default_fee_tenure'] == '3 Months' ? 'selected' : '' }}>3 Months</option>
                                        <option value="6 Months" {{ $defaults['financial']['default_fee_tenure'] == '6 Months' ? 'selected' : '' }}>6 Months</option>
                                        <option value="1 Year" {{ $defaults['financial']['default_fee_tenure'] == '1 Year' ? 'selected' : '' }}>1 Year</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: CUSTOM WORDING & TERMS -->
                        <div class="tab-pane fade" id="v-pills-wording" role="tabpanel">
                            <div class="section-header">
                                <h5 class="fw-bold mb-1 text-dark-title"><i class="fas fa-file-signature text-first me-2"></i>Custom Wording & Terms</h5>
                                <p class="text-muted small mb-0">Customize text & legal wording for invoices, receipts, salary slips, and welcome messages.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label-custom">Invoice Terms & Conditions Wording</label>
                                    <textarea name="word_invoice_terms" rows="4" class="form-control-custom">{{ $defaults['wording']['invoice_terms'] }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-custom">Receipt Footer Note</label>
                                    <textarea name="word_receipt_footer" rows="2" class="form-control-custom">{{ $defaults['wording']['receipt_footer'] }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-custom">Salary Slip Disclaimer Wording</label>
                                    <textarea name="word_salary_slip_note" rows="2" class="form-control-custom">{{ $defaults['wording']['salary_slip_note'] }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-custom">Welcome Email Text</label>
                                    <textarea name="word_welcome_email_text" rows="3" class="form-control-custom">{{ $defaults['wording']['welcome_email_text'] }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: APPEARANCE & THEME -->
                        <div class="tab-pane fade" id="v-pills-appearance" role="tabpanel">
                            <div class="section-header">
                                <h5 class="fw-bold mb-1 text-dark-title"><i class="fas fa-palette text-first me-2"></i>Appearance & Theme Controls</h5>
                                <p class="text-muted small mb-0">System-wide font family, primary accent color, and theme defaults.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Primary Accent Color</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="color" name="app_primary_color" value="{{ $defaults['appearance']['primary_color'] }}" class="form-control-color" style="width:50px; height:42px; border-radius:8px; border:1px solid var(--border); cursor:pointer;" />
                                        <span class="fw-bold font-monospace">{{ $defaults['appearance']['primary_color'] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Default Theme Mode</label>
                                    <select name="app_default_theme" class="form-control-custom">
                                        <option value="light" {{ $defaults['appearance']['default_theme'] == 'light' ? 'selected' : '' }}>Light Theme</option>
                                        <option value="dark" {{ $defaults['appearance']['default_theme'] == 'dark' ? 'selected' : '' }}>Dark Theme</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">System Font Family</label>
                                    <select name="app_font_family" class="form-control-custom">
                                        <option value="Poppins" {{ $defaults['appearance']['font_family'] == 'Poppins' ? 'selected' : '' }}>Poppins (Sans-Serif - Recommended)</option>
                                        <option value="Inter" {{ $defaults['appearance']['font_family'] == 'Inter' ? 'selected' : '' }}>Inter</option>
                                        <option value="Roboto" {{ $defaults['appearance']['font_family'] == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 5: SECURITY & TOOLS -->
                        <div class="tab-pane fade" id="v-pills-security" role="tabpanel">
                            <div class="section-header">
                                <h5 class="fw-bold mb-1 text-dark-title"><i class="fas fa-shield-alt text-first me-2"></i>Security & Administrative Tools</h5>
                                <p class="text-muted small mb-0">System security policies, subadmin access permissions, and database backup controls.</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Minimum Password Length</label>
                                    <input type="number" name="sec_min_password_length" value="{{ $defaults['security']['min_password_length'] }}" min="6" max="32" class="form-control-custom" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">SubAdmin Password Reset Permission</label>
                                    <select name="sec_allow_subadmin_password_reset" class="form-control-custom">
                                        <option value="1" {{ $defaults['security']['allow_subadmin_password_reset'] == '1' ? 'selected' : '' }}>Allowed</option>
                                        <option value="0" {{ $defaults['security']['allow_subadmin_password_reset'] == '0' ? 'selected' : '' }}>Super Admin Only</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-4 pt-3 border-top d-flex gap-3 flex-wrap">
                                    <a href="{{ route('backups.index') }}" class="btn btn-outline-primary px-4 py-2" style="border-radius:10px;">
                                        <i class="fas fa-database me-2"></i>Manage Database Backups
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 pt-4 border-top d-flex justify-content-end gap-3">
                        <button type="reset" class="button button-secondary px-4 py-2">
                            <i class="fas fa-undo me-2"></i>Reset Defaults
                        </button>
                        <button type="submit" class="button button-primary px-5 py-2">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
