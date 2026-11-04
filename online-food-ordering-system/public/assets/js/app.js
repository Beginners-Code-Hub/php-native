/* FoodDash browser script: modals, AJAX forms, confirm dialogs, toasts, cart. */
(() => {
    const body = document.body;
    const base = body.dataset.base.replace(/\/$/, '');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const icons = { success: 'check_circle', error: 'error', info: 'info', warning: 'warning' };

    const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    /* ---------- toasts ---------- */
    function toast(message, type = 'info') {
        M.toast({
            html: `<i class="material-icons">${icons[type] || 'info'}</i><span>${esc(message)}</span>`,
            classes: `fd-toast fd-toast-${type}`,
            displayLength: 4500,
        });
    }
    window.FD = { toast, base, token };

    /* ---------- fetch helper ---------- */
    async function send(url, { method = 'GET', data = {}, form = null } = {}) {
        const headers = { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': token, Accept: 'application/json, text/html' };
        const options = { method: method === 'GET' ? 'GET' : 'POST', headers, credentials: 'same-origin' };
        if (method !== 'GET') {
            const payload = form ? new FormData(form) : new FormData();
            Object.entries(data).forEach(([k, v]) => payload.append(k, v));
            if (method !== 'POST') payload.set('_method', method);
            payload.set('_token', token);
            options.body = payload;
        }
        const res = await fetch(url, options);
        const type = res.headers.get('content-type') || '';
        const json = type.includes('json') ? await res.json() : null;
        return { res, json, html: json ? null : await res.text() };
    }

    /* ---------- the shared modal ---------- */
    const modalEl = document.getElementById('app-modal');
    const modalBody = modalEl.querySelector('.fd-modal-body');
    const modal = M.Modal.init(modalEl, { onCloseEnd: () => { modalBody.innerHTML = ''; } });

    function mount(html) {
        modalBody.innerHTML = html;
        M.updateTextFields();
        M.FormSelect.init(modalBody.querySelectorAll('select'));
        const first = modalBody.querySelector('input:not([type=hidden]):not([type=file]), select');
        if (first && first.tagName === 'INPUT') setTimeout(() => first.focus(), 250);
        if (!modal.isOpen) modal.open();
    }

    async function openModal(url) {
        const { res, json, html } = await send(url);
        if (res.status === 401 && json && json.login) {
            toast('Please log in first.', 'info');
            return openModal(base + '/login');
        }
        if (!res.ok) return toast((json && json.message) || 'Could not open that form.', 'error');
        mount(html);
    }

    /* ---------- reacting to a JSON reply ---------- */
    function showErrors(scope, json) {
        scope.querySelectorAll('[data-error]').forEach((n) => { n.textContent = ''; });
        scope.querySelectorAll('.invalid').forEach((n) => n.classList.remove('invalid'));
        const alertBox = scope.querySelector('[data-form-alert]');
        if (alertBox) { alertBox.classList.add('hide'); alertBox.textContent = ''; }
        if (!json || json.ok) return;
        Object.entries(json.errors || {}).forEach(([name, msg]) => {
            const slot = scope.querySelector(`[data-error="${name}"]`);
            if (slot) slot.textContent = msg;
            const input = scope.querySelector(`[name="${name}"]`);
            if (input) input.classList.add('invalid');
        });
        if (alertBox && json.message) { alertBox.textContent = json.message; alertBox.classList.remove('hide'); }
    }

    function handle(json, scope) {
        if (!json) return;
        if (typeof json.count === 'number') document.querySelectorAll('[data-cart-count]').forEach((n) => { n.textContent = json.count; });
        if (!json.ok) {
            if (scope) showErrors(scope, json);
            if (!json.errors || !Object.keys(json.errors).length) toast(json.message || 'Something went wrong.', 'error');
            return;
        }
        if (json.redirect) return void (window.location.href = json.redirect);
        if (json.reload) return void window.location.reload();
        if (json.message) toast(json.message, 'success');
        if (modal.isOpen && scope && scope.matches('form')) modal.close();
    }

    /* ---------- click handling ---------- */
    document.addEventListener('click', async (e) => {
        const target = e.target.closest('[data-modal], [data-confirm], [data-post], [data-add-to-cart], [data-cart-qty], [data-cart-clear]');
        if (!target) return;
        e.preventDefault();

        if (target.matches('[data-modal]')) return openModal(target.dataset.modal);

        if (target.matches('[data-confirm]')) return confirmThen(target);

        if (target.matches('[data-post]')) {
            const { json } = await send(target.dataset.post, { method: 'POST', data: JSON.parse(target.dataset.fields || '{}') });
            return handle(json, null);
        }

        if (target.matches('[data-add-to-cart]')) {
            const { json } = await send(base + '/cart/add', { method: 'POST', data: { product_id: target.dataset.addToCart } });
            target.classList.add('pulse');
            setTimeout(() => target.classList.remove('pulse'), 600);
            return handle(json, null);
        }

        if (target.matches('[data-cart-qty]')) {
            const { json } = await send(base + '/cart/update', { method: 'POST', data: { product_id: target.dataset.id, qty: target.dataset.cartQty } });
            if (json && json.html) { mount(json.html); handle({ ok: true, count: json.count }, null); }
            return;
        }

        if (target.matches('[data-cart-clear]')) {
            const { json } = await send(base + '/cart/clear', { method: 'POST' });
            if (json && json.html) { mount(json.html); handle({ ok: true, count: 0, message: json.message }, null); }
        }
    });

    /* ---------- confirmation dialog ---------- */
    const confirmEl = document.getElementById('confirm-modal');
    const confirmModal = M.Modal.init(confirmEl);
    const yesBtn = confirmEl.querySelector('[data-confirm-yes]');
    const tones = { danger: ['warning', 'red'], success: ['task_alt', 'green'], info: ['help_outline', 'blue-grey'] };

    function confirmThen(target) {
        const tone = tones[target.dataset.tone] || tones.info;
        confirmEl.querySelector('[data-confirm-text]').textContent = target.dataset.confirm;
        confirmEl.querySelector('.fd-confirm-icon').className = `fd-confirm-icon fd-tone-${target.dataset.tone || 'info'}`;
        confirmEl.querySelector('.fd-confirm-icon i').textContent = tone[0];
        yesBtn.textContent = target.dataset.yes || 'Yes, continue';
        yesBtn.className = `btn waves-effect ${tone[1]}`;
        yesBtn.onclick = async () => {
            yesBtn.disabled = true;
            const { json } = await send(target.dataset.url, { method: target.dataset.method || 'POST', data: JSON.parse(target.dataset.fields || '{}') });
            yesBtn.disabled = false;
            confirmModal.close();
            handle(json, null);
        };
        confirmModal.open();
    }

    /* ---------- AJAX forms ---------- */
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-ajax]');
        if (!form) return;
        e.preventDefault();
        const button = form.querySelector('[type=submit]');
        const label = button.innerHTML;
        button.disabled = true;
        button.innerHTML = 'Please wait...';
        showErrors(form, null);
        try {
            const { json } = await send(form.action, { method: 'POST', form });
            handle(json, form);
        } catch (err) {
            toast('Network problem. Please try again.', 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = label;
        }
    });

    /* ---------- start-up ---------- */
    document.addEventListener('DOMContentLoaded', () => {
        (window.__flash || []).forEach((f) => toast(f.message, f.type));
        if (new URLSearchParams(location.search).get('login') === '1') openModal(base + '/login');
    });
    if (document.readyState !== 'loading') {
        (window.__flash || []).forEach((f) => toast(f.message, f.type));
        if (new URLSearchParams(location.search).get('login') === '1') openModal(base + '/login');
    }
})();
