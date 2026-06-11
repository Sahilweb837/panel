@extends('layouts.app')
@section('title', 'Payroll Settings')
@section('page-title', 'Payroll & Razorpay Settings')

@section('content')
<style>
    .settings-section { border-radius: 12px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 24px; }
    .settings-section-header { padding: 16px 24px; background: var(--surface-soft); border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
    .settings-section-body { padding: 24px; }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 999px; font-size: .8rem; font-weight: 600; }
    .blink { animation: blink-anim 1.2s infinite; }
    @keyframes blink-anim { 0%,100%{opacity:1} 50%{opacity:0.3} }
    .mode-badge-test { background: rgba(245,158,11,0.12); color: #f59e0b; }
    .mode-badge-live { background: rgba(16,185,129,0.12); color: #10b981; }
    .copy-btn { cursor: pointer; color: var(--muted); transition: color 0.2s; }
    .copy-btn:hover { color: var(--first-color); }
    .info-card { background: rgba(255,85,50,0.06); border: 1px solid rgba(255,85,50,0.2); border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }
</style>

<div class="row g-4">

    {{-- LEFT: Razorpay API Config --}}
    <div class="col-12 col-lg-7">

        {{-- Info Banner --}}
        <div class="info-card d-flex gap-3 align-items-start">
            <i class="fas fa-exclamation-triangle text-first mt-1" style="font-size:1.2rem;flex-shrink:0;"></i>
            <div>
                <strong>RazorpayX Required</strong>
                <p class="mb-1 text-muted small">This module uses <strong>Razorpay Payouts API (RazorpayX)</strong> to transfer salary directly to employee bank accounts. You need a RazorpayX Current Account — different from a regular Razorpay gateway.</p>
                <a href="https://razorpay.com/x" target="_blank" class="small text-first fw-bold"><i class="fas fa-external-link-alt me-1"></i>Apply for RazorpayX →</a>
            </div>
        </div>

        <div class="settings-section">
            <div class="settings-section-header">
                <i class="fas fa-key text-first"></i>
                <h5 class="mb-0 fw-bold">Razorpay API Credentials</h5>
                <span class="ms-auto status-pill {{ $settings->mode === 'live' ? 'mode-badge-live' : 'mode-badge-test' }}">
                    <i class="fas fa-circle blink"></i>
                    {{ strtoupper($settings->mode) }} MODE
                </span>
            </div>
            <div class="settings-section-body">
                <form action="{{ route('payroll.settings.save') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mode</label>
                            <div class="d-flex gap-3">
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                    <input type="radio" name="mode" value="test" {{ $settings->mode === 'test' ? 'checked' : '' }} /> Test Mode
                                </label>
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                    <input type="radio" name="mode" value="live" {{ $settings->mode === 'live' ? 'checked' : '' }} /> Live Mode
                                </label>
                            </div>
                            <small class="text-muted">Use <strong>Test Mode</strong> first. No real money is moved in test.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">RazorpayX API Key ID <span class="text-danger">*</span></label>
                            <input type="text" name="razorpay_key_id" class="form-input" value="{{ old('razorpay_key_id', $settings->razorpay_key_id) }}" placeholder="rzp_test_xxxxxxxxxxxx" required />
                            <small class="text-muted">Found in RazorpayX Dashboard → Settings → API Keys</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">RazorpayX API Key Secret <span class="text-danger">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="razorpay_key_secret" class="form-input" id="secret-input" placeholder="{{ $settings->razorpay_key_secret ? '••••••••••••••••• (saved — enter new to change)' : 'Enter your API secret' }}" />
                                <button type="button" class="btn btn-link position-absolute" style="right:10px;top:50%;transform:translateY(-50%);padding:0;" onclick="toggleSecretVisibility()">
                                    <i class="fas fa-eye text-muted" id="secret-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Stored encrypted. Leave blank to keep existing secret.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">RazorpayX Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="razorpay_account_number" class="form-input" value="{{ old('razorpay_account_number', $settings->razorpay_account_number) }}" placeholder="e.g. 7878780076890900" required />
                            <small class="text-muted">Your RazorpayX virtual current account number (from which payouts are made)</small>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="button button-primary px-5 py-2">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Webhook Config --}}
        <div class="settings-section">
            <div class="settings-section-header">
                <i class="fas fa-satellite-dish text-first"></i>
                <h5 class="mb-0 fw-bold">Webhook Configuration</h5>
            </div>
            <div class="settings-section-body">
                <p class="text-muted small mb-3">Add this webhook URL in your <strong>RazorpayX Dashboard → Webhooks</strong> to automatically update payout status when Razorpay processes or fails the transfer.</p>
                <div class="d-flex align-items-center gap-2">
                    <code class="p-2 rounded flex-grow-1" style="background:var(--surface-soft);font-size:.85rem;word-break:break-all;" id="webhook-url">{{ route('payroll.webhook') }}</code>
                    <button type="button" class="copy-btn" onclick="copyWebhookUrl()" title="Copy URL">
                        <i class="fas fa-copy fa-lg"></i>
                    </button>
                </div>
                <div class="mt-3">
                    <p class="small text-muted mb-1"><strong>Subscribe to these events:</strong></p>
                    <ul class="small text-muted">
                        <li><code>payout.processed</code> — Marks salary slip as Paid</li>
                        <li><code>payout.failed</code> — Resets slip to allow retry</li>
                        <li><code>payout.reversed</code> — Resets slip to allow retry</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Bulk Payout + Status --}}
    <div class="col-12 col-lg-5">

        {{-- Bulk Payout --}}
        <div class="settings-section">
            <div class="settings-section-header">
                <i class="fas fa-paper-plane text-first"></i>
                <h5 class="mb-0 fw-bold">Bulk Payout</h5>
            </div>
            <div class="settings-section-body">
                <p class="text-muted small mb-3">Pay all <strong>Pending</strong> salary slips for a selected month at once. Each employee must have bank details set.</p>
                <form action="{{ route('payroll.bulk-payout') }}" method="POST" onsubmit="return confirm('This will initiate REAL bank transfers for all pending salary slips. Are you sure?')">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Month</label>
                            <select name="month" class="form-input" required>
                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                    <option value="{{ $m }}" {{ now()->format('F') === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Year</label>
                            <input type="number" name="year" class="form-input" value="{{ now()->year }}" min="2020" max="{{ now()->year + 1 }}" required />
                        </div>
                        <div class="col-12">
                            <button type="submit" class="button button-primary w-100 py-2">
                                <i class="fas fa-paper-plane me-2"></i>Initiate Bulk Payout
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="settings-section">
            <div class="settings-section-header">
                <i class="fas fa-bolt text-first"></i>
                <h5 class="mb-0 fw-bold">Quick Links</h5>
            </div>
            <div class="settings-section-body d-flex flex-column gap-2">
                <a href="{{ route('salary_slips.index') }}" class="button button-secondary w-100 py-2">
                    <i class="fas fa-wallet me-2"></i>View All Salary Slips
                </a>
                <a href="{{ route('employees.index') }}" class="button button-secondary w-100 py-2">
                    <i class="fas fa-users me-2"></i>Manage Employees / Bank Details
                </a>
                <a href="{{ route('salary_slips.create') }}" class="button button-secondary w-100 py-2">
                    <i class="fas fa-plus me-2"></i>Generate New Salary Slip
                </a>
            </div>
        </div>

        {{-- Payout Status Legend --}}
        <div class="settings-section">
            <div class="settings-section-header">
                <i class="fas fa-info-circle text-first"></i>
                <h5 class="mb-0 fw-bold">Payout Status Guide</h5>
            </div>
            <div class="settings-section-body">
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-secondary">queued</span> Transfer queued, will process when balance available</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-warning text-dark">processing</span> Transfer in progress</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-success">processed</span> Successfully transferred to employee bank</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-danger">failed</span> Transfer failed — can be retried</div>
                    <div class="d-flex align-items-center gap-2"><span class="badge bg-danger">reversed</span> Transfer reversed — can be retried</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSecretVisibility() {
    const input = document.getElementById('secret-input');
    const eye   = document.getElementById('secret-eye');
    if (input.type === 'password') {
        input.type = 'text';
        eye.className = 'fas fa-eye-slash text-muted';
    } else {
        input.type = 'password';
        eye.className = 'fas fa-eye text-muted';
    }
}
function copyWebhookUrl() {
    const url = document.getElementById('webhook-url').textContent;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.querySelector('.copy-btn i');
        btn.className = 'fas fa-check text-success fa-lg';
        setTimeout(() => btn.className = 'fas fa-copy fa-lg', 2000);
    });
}
</script>
@endsection
