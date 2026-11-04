/* Rider page: "Simulate GPS" walks an L-shaped route and posts each point to the server.
   A real phone would call the same endpoint with navigator.geolocation coordinates. */
(() => {
    const STEP = 3;           // map units per tick
    const INTERVAL = 1200;    // ms between pings (override: <body data-ping="600">)

    function route(ox, oy, dx, dy) {
        const pts = [];
        const walk = (x0, y0, x1, y1) => {
            const dist = Math.hypot(x1 - x0, y1 - y0);
            const n = Math.max(1, Math.ceil(dist / STEP));
            for (let i = 1; i <= n; i++) pts.push({ x: x0 + ((x1 - x0) * i) / n, y: y0 + ((y1 - y0) * i) / n });
        };
        walk(ox, oy, dx, oy);   // along the kitchen's street
        walk(dx, oy, dx, dy);   // then along the customer's street
        return pts;
    }

    document.querySelectorAll('[data-rider-order]').forEach((card) => {
        const svg = card.querySelector('svg.fd-map');
        const pin = svg.querySelector('[data-rider]');
        const bar = card.querySelector('.determinate');
        const button = card.querySelector('[data-rider-start]');
        const { ox, oy, dx, dy } = Object.fromEntries(Object.entries(svg.dataset).map(([k, v]) => [k, Number(v)]));
        const pts = route(ox, oy, dx, dy);
        const interval = Number(document.body.dataset.ping || INTERVAL);

        button.addEventListener('click', async () => {
            button.disabled = true;
            window.FD.toast('GPS started. Customer can see you now.', 'info');
            for (let i = 0; i < pts.length; i++) {
                const last = i === pts.length - 1;
                const form = new FormData();
                form.set('x', pts[i].x.toFixed(2));
                form.set('y', pts[i].y.toFixed(2));
                form.set('_token', window.FD.token);
                if (last) form.set('arrived', '1');
                const res = await fetch(card.dataset.endpoint, { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': window.FD.token }, credentials: 'same-origin' });
                const json = await res.json();
                if (!json.ok) return window.FD.toast(json.message || 'Could not send location.', 'error');
                pin.style.transform = `translate(${pts[i].x}px, ${pts[i].y}px)`;
                bar.style.width = `${Math.round(((i + 1) / pts.length) * 100)}%`;
                if (last) {
                    window.FD.toast(json.message, 'success');
                    setTimeout(() => window.location.reload(), 2200);
                } else {
                    await new Promise((r) => setTimeout(r, interval));
                }
            }
        });
    });
})();
