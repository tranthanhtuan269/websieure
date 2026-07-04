@php
    $generationId = $generationId ?? null;
    $defaultPageType = $defaultPageType ?? 'standard';
    $deployLocalEnabled = $deployLocalEnabled ?? config('landing.deploy.local_enabled');
    $variant = $variant ?? 'dark';
    $formId = 'deployForm_' . ($generationId ?? 'new');
@endphp
<form id="{{ $formId }}" class="lp-deploy-form {{ $variant === 'light' ? 'lp-deploy-form--light' : '' }}" data-generation-id="{{ $generationId }}">
    @csrf
    <div class="lp-deploy-form__grid">
        <div class="form-group">
            <label>Landing page cần deploy <span style="color:#ef4444">*</span></label>
            <select name="page_type" required>
                <option value="standard" @selected($defaultPageType === 'standard')>Chuẩn ~3000 từ (3 ảnh)</option>
                <option value="compare" @selected($defaultPageType === 'compare')>Bảng so sánh đối thủ</option>
                <option value="popup" @selected($defaultPageType === 'popup')>Popup Cookie Notice</option>
            </select>
        </div>
        <div class="form-group">
            <label>Phương thức <span style="color:#ef4444">*</span></label>
            <select name="method" class="deploy-method-select" required>
                <option value="sftp">SFTP (hosting VPS / cPanel)</option>
                @if($deployLocalEnabled)
                <option value="local">Local server ({{ config('landing.deploy.local_base_path') }})</option>
                @endif
            </select>
        </div>
    </div>

    <div class="deploy-fields-sftp">
        <div class="lp-deploy-form__grid">
            <div class="form-group">
                <label>Host SFTP <span style="color:#ef4444">*</span></label>
                <input type="text" name="host" placeholder="178.104.222.35 hoặc ftp.hosting.com">
            </div>
            <div class="form-group">
                <label>Port</label>
                <input type="number" name="port" value="22" min="1" max="65535">
            </div>
            <div class="form-group">
                <label>Username <span style="color:#ef4444">*</span></label>
                <input type="text" name="username" placeholder="root hoặc ftp_user" autocomplete="username">
            </div>
            <div class="form-group">
                <label>Password <span style="color:#ef4444">*</span></label>
                <input type="password" name="password" placeholder="••••••••" autocomplete="current-password">
            </div>
        </div>
        <div class="form-group">
            <label>Đường dẫn remote <span style="color:#ef4444">*</span></label>
            <input type="text" name="remote_path" placeholder="/var/www/mysite.com hoặc /public_html/landing">
            <small style="color:#94a3b8;display:block;margin-top:.35rem;">Thư mục document root trên hosting (chứa index.html sau khi deploy).</small>
        </div>
    </div>

    @if($deployLocalEnabled)
    <div class="deploy-fields-local" hidden>
        <div class="form-group">
            <label>Tên thư mục trên server <span style="color:#ef4444">*</span></label>
            <input type="text" name="local_folder" placeholder="offer.lamwebre.com">
            <small style="color:#94a3b8;display:block;margin-top:.35rem;">
                File sẽ được copy vào <code>{{ config('landing.deploy.local_base_path') }}/tên-thư-mục</code>
            </small>
        </div>
    </div>
    @endif

    <div class="form-group">
        <label>URL sau deploy (tùy chọn)</label>
        <input type="url" name="site_url" placeholder="https://offer.lamwebre.com">
        <small style="color:#94a3b8;display:block;margin-top:.35rem;">Hiển thị link trực tiếp sau khi deploy thành công.</small>
    </div>

    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <button type="submit" class="btn btn-primary deploy-submit-btn">Deploy lên hosting</button>
        <span class="deploy-status" style="font-size:.9rem;"></span>
    </div>
</form>

<style>
.lp-deploy-form { max-width: 720px; color: #e2e8f0; }
.lp-deploy-form__grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: .75rem 1rem; }
.lp-deploy-form .form-group { margin-bottom: .85rem; }
.lp-deploy-form label { display: block; font-size: .85rem; margin-bottom: .3rem; color: #cbd5e1; }
.lp-deploy-form input, .lp-deploy-form select {
    width: 100%; padding: .5rem .65rem; border-radius: 8px;
    border: 1px solid #475569; background: #1e293b; color: #f8fafc;
}
.lp-deploy-form .deploy-status--ok { color: #4ade80; }
.lp-deploy-form .deploy-status--err { color: #f87171; }
.lp-deploy-form--light { color: inherit; max-width: 100%; }
.lp-deploy-form--light label { color: var(--muted, #64748b); }
.lp-deploy-form--light input,
.lp-deploy-form--light select {
    border: 1px solid var(--border, #e2e8f0);
    background: #fff;
    color: inherit;
}
</style>

<script>
(function () {
    var form = document.getElementById(@json($formId));
    if (!form) return;

    var methodSelect = form.querySelector('.deploy-method-select');
    var sftpFields = form.querySelector('.deploy-fields-sftp');
    var localFields = form.querySelector('.deploy-fields-local');
    var statusEl = form.querySelector('.deploy-status');
    var submitBtn = form.querySelector('.deploy-submit-btn');
    var generationId = form.dataset.generationId;

    function csrf() {
        var input = form.querySelector('input[name=_token]');
        return input ? input.value : '';
    }

    function toggleMethod() {
        var method = methodSelect ? methodSelect.value : 'sftp';
        if (sftpFields) sftpFields.hidden = method === 'local';
        if (localFields) localFields.hidden = method !== 'local';
    }

    if (methodSelect) {
        methodSelect.addEventListener('change', toggleMethod);
        toggleMethod();
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        statusEl.textContent = 'Đang deploy...';
        statusEl.className = 'deploy-status';
        submitBtn.disabled = true;

        var method = methodSelect.value;
        var body = {
            page_type: form.page_type.value,
            method: method,
            site_url: form.site_url.value || null,
        };

        if (method === 'local') {
            body.remote_path = form.local_folder ? form.local_folder.value : '';
        } else {
            body.host = form.host.value;
            body.port = parseInt(form.port.value || '22', 10);
            body.username = form.username.value;
            body.password = form.password.value;
            body.remote_path = form.remote_path.value;
        }

        try {
            var res = await fetch('/admin/landing-pages/generator/' + generationId + '/deploy', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            });
            var data = await res.json();
            if (!res.ok) throw new Error(data.message || data.error || 'Deploy thất bại');

            statusEl.className = 'deploy-status deploy-status--ok';
            var msg = data.message || 'Deploy thành công!';
            if (data.site_url) {
                msg += ' — <a href="' + data.site_url + '" target="_blank" rel="noopener" style="color:#4ade80">' + data.site_url + '</a>';
            }
            statusEl.innerHTML = msg;
        } catch (err) {
            statusEl.className = 'deploy-status deploy-status--err';
            statusEl.textContent = err.message || 'Có lỗi khi deploy.';
        } finally {
            submitBtn.disabled = false;
        }
    });
})();
</script>
