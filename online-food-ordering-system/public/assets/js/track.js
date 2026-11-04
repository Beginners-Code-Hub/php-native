/* Customer tracking page: ask the server for the order every 3 seconds and move the pin. */
(() => {
    const root = document.getElementById('track');
    if (!root) return;

    const endpoint = root.dataset.endpoint;
    const pin = document.querySelector('[data-rider]');
    const steps = [...root.querySelectorAll('[data-step]')];
    let lastStatus = root.dataset.status;
    let timer = null;

    function movePin(p) {
        pin.style.transform = `translate(${p.x}px, ${p.y}px)`;
    }

    function render(t) {
        root.querySelector('[data-status-label]').textContent = t.label;
        root.querySelector('[data-status-label]').className = `fd-status fd-status-${t.status}`;
        movePin(t.position);

        const cancelled = t.status === 'cancelled';
        root.querySelector('[data-cancelled]').classList.toggle('hide', !cancelled);
        steps.forEach((li) => {
            const n = Number(li.dataset.step);
            li.classList.toggle('done', !cancelled && n < t.step);
            li.classList.toggle('current', !cancelled && n === t.step);
            const hit = t.log.find((l) => l.status === li.dataset.status);
            li.querySelector('[data-step-time]').textContent = hit ? hit.at : '';
        });

        const rider = root.querySelector('[data-rider-name]');
        if (rider && t.rider) rider.textContent = `${t.rider.name} is your rider`;
        const cancelBox = root.querySelector('[data-cancel-box]');
        if (cancelBox && t.status !== 'pending') cancelBox.remove();

        if (t.status !== lastStatus) {
            const type = t.status === 'cancelled' ? 'warning' : 'success';
            const text = t.status === 'delivered' ? 'Delivered! Enjoy your meal.' : `Your order is now: ${t.label}`;
            window.FD.toast(text, type);
            lastStatus = t.status;
        }
        if (t.status === 'delivered' || cancelled) clearInterval(timer);
    }

    async function poll() {
        try {
            const res = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }, credentials: 'same-origin' });
            if (res.ok) render(await res.json());
        } catch (e) { /* offline for a moment: try again on the next tick */ }
    }

    render(window.__tracking);
    if (!['delivered', 'cancelled'].includes(lastStatus)) timer = setInterval(poll, 3000);
})();
