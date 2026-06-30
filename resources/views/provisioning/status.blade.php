@extends('layouts.app')

@section('title', 'Đang cài đặt website')

@section('content')
<section class="section">
    <div class="container" style="max-width:720px;">
        <div class="form-card">
            <h1 style="margin-bottom:.35rem;">Đang kích hoạt website</h1>
            <p style="color:var(--muted);margin-bottom:1.25rem;">
                Đơn <strong>{{ $order->order_code }}</strong> — {{ $order->domain }}
            </p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="provision-steps" id="provision-steps">
                @foreach($steps as $step)
                    <div class="provision-step" data-step="{{ $step->value }}">
                        <span class="provision-step-icon">○</span>
                        <span>{{ $step->label() }}</span>
                    </div>
                @endforeach
            </div>

            <div id="provision-status" style="margin:1.25rem 0;">
                <p><strong>Trạng thái:</strong> <span id="status-label">{{ $status?->label() ?? 'Đang chờ...' }}</span></p>
            </div>

            <div id="provision-error" class="alert alert-error" style="display:none;"></div>

            <div id="provision-result" style="display:none;">
                <div class="alert alert-success">
                    <p><strong>Website đã sẵn sàng!</strong></p>
                    <p style="margin-top:.5rem;">Link: <a id="site-url" href="#" target="_blank" rel="noopener"></a></p>
                    <p style="margin-top:.5rem;">WP Admin: <strong id="wp-user"></strong> / <strong id="wp-pass"></strong></p>
                    <p style="font-size:.85rem;margin-top:.5rem;">Thông tin đăng nhập cũng được gửi qua email {{ $order->customer_email }}</p>
                </div>
            </div>

            <div id="provision-log" style="margin-top:1rem;">
                <strong>Nhật ký:</strong>
                <ul id="log-list" style="margin-top:.5rem;font-size:.9rem;color:var(--muted);">
                    @foreach($order->provisioning_log ?? [] as $entry)
                        <li>{{ $entry['message'] ?? '' }}</li>
                    @endforeach
                </ul>
            </div>

            <form id="retry-form" method="POST" action="{{ route('provisioning.retry', ['order' => $order, 'token' => $token]) }}" style="display:none;margin-top:1rem;">
                @csrf
                <button type="submit" class="btn btn-primary">Thử cài lại</button>
            </form>

            <div style="margin-top:1.5rem;display:flex;gap:.6rem;flex-wrap:wrap;">
                <a href="{{ route('home') }}" class="btn btn-outline">Về trang chủ</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.provision-steps { display: flex; flex-direction: column; gap: .5rem; }
.provision-step { display: flex; align-items: center; gap: .75rem; padding: .65rem .85rem; border-radius: 10px; background: #f8fafc; color: var(--muted); }
.provision-step.active { background: #f5f3ff; color: var(--primary); font-weight: 600; }
.provision-step.done { color: #047857; }
.provision-step.done .provision-step-icon::before { content: '✓'; }
.provision-step.failed { background: #fef2f2; color: #991b1b; }
.provision-step-icon { width: 1.25rem; text-align: center; font-weight: 700; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const pollUrl = @json(route('provisioning.poll', ['order' => $order, 'token' => $token]));
    const statusLabel = document.getElementById('status-label');
    const errorBox = document.getElementById('provision-error');
    const resultBox = document.getElementById('provision-result');
    const retryForm = document.getElementById('retry-form');
    const logList = document.getElementById('log-list');
    const steps = document.querySelectorAll('.provision-step');

    function updateSteps(current) {
        const order = ['queued', 'configuring_dns', 'installing_wordpress', 'configuring_ssl', 'completed'];
        const idx = order.indexOf(current);
        steps.forEach(el => {
            const step = el.dataset.step;
            const stepIdx = order.indexOf(step);
            el.classList.remove('active', 'done', 'failed');
            if (current === 'failed' && step === current) {
                el.classList.add('failed');
            } else if (stepIdx < idx) {
                el.classList.add('done');
            } else if (stepIdx === idx) {
                el.classList.add('active');
            }
        });
    }

    function poll() {
        fetch(pollUrl, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                statusLabel.textContent = data.status_label || 'Đang xử lý...';
                updateSteps(data.status);

                if (data.log?.length) {
                    logList.innerHTML = data.log.map(e => `<li>${e.message || ''}</li>`).join('');
                }

                if (data.failed) {
                    errorBox.style.display = 'block';
                    errorBox.textContent = data.error || 'Cài đặt thất bại.';
                    retryForm.style.display = 'block';
                    return;
                }

                if (data.completed) {
                    resultBox.style.display = 'block';
                    const link = document.getElementById('site-url');
                    link.href = data.site_url;
                    link.textContent = data.site_url;
                    if (data.wp_admin_user) {
                        document.getElementById('wp-user').textContent = data.wp_admin_user;
                        document.getElementById('wp-pass').textContent = data.wp_admin_pass || '—';
                    }
                    return;
                }

                setTimeout(poll, 3000);
            })
            .catch(() => setTimeout(poll, 5000));
    }

    poll();
})();
</script>
@endpush
