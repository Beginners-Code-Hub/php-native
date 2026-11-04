<?php
// End-to-end smoke test over HTTP.
//   php -S localhost:8000 -t public      (in one terminal)
//   php tests/smoke.php [http://localhost:8000]
// Run `php database/install.php` afterwards to reset the demo data.

$base = rtrim($argv[1] ?? 'http://localhost:8000', '/');
$failures = 0;

function check(string $label, bool $ok, string $detail = ''): void
{
    global $failures;
    echo ($ok ? '  ok   ' : '  FAIL ') . $label . ($ok ? '' : "  -> $detail") . "\n";
    if (!$ok) {
        $failures++;
    }
}

/** A tiny browser: one cookie jar per user. */
final class Client
{
    private string $jar;
    public string $token = '';

    public function __construct(private string $base)
    {
        $this->jar = tempnam(sys_get_temp_dir(), 'fd');
        $this->refreshToken();
    }

    public function refreshToken(): void
    {
        $page = $this->request('GET', '/', [], false)['body'];
        preg_match('/name="csrf-token" content="([a-f0-9]+)"/', $page, $m);
        $this->token = $m[1] ?? '';
    }

    public function request(string $method, string $path, array $data = [], bool $ajax = true, bool $csrf = true): array
    {
        $ch = curl_init($this->base . $path);
        $headers = $ajax ? ['X-Requested-With: XMLHttpRequest', 'Accept: application/json, text/html'] : [];
        if ($csrf && $method !== 'GET') {
            $headers[] = 'X-CSRF-Token: ' . $this->token;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_COOKIEJAR => $this->jar,
            CURLOPT_COOKIEFILE => $this->jar,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $body = (string) curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['status' => $status, 'body' => $body, 'json' => json_decode($body, true)];
    }

    public function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, $data);
    }
}

echo "FoodDash smoke test against $base\n\n";

// ---- public pages -------------------------------------------------------
$guest = new Client($base);
$home = $guest->request('GET', '/', [], false);
check('menu page loads', $home['status'] === 200 && str_contains($home['body'], 'Classic Cheeseburger'));
check('footer says Powered by Beginners Code Hub', str_contains($home['body'], 'Powered by <strong>Beginners Code Hub</strong>'));
check('unknown page is a 404', $guest->request('GET', '/nope', [], false)['status'] === 404);
check('POST without CSRF token is refused (419)', $guest->request('POST', '/login', ['email' => 'a@b.c'], true, false)['status'] === 419);

// ---- accounts -----------------------------------------------------------
$email = 'smoke' . random_int(1000, 99999) . '@example.com';
$cust = new Client($base);
$bad = $cust->post('/register', ['name' => '', 'email' => 'nope', 'password' => '123']);
check('register validation returns 422 with field errors', $bad['status'] === 422 && isset($bad['json']['errors']['email'], $bad['json']['errors']['name']));
$ok = $cust->post('/register', ['name' => 'Smoke Tester', 'email' => $email, 'password' => 'Demo@1234', 'password_confirmation' => 'Demo@1234']);
check('register works', $ok['status'] === 200 && ($ok['json']['ok'] ?? false), $ok['body']);
$dup = (new Client($base));
$dupRes = $dup->post('/register', ['name' => 'Again', 'email' => $email, 'password' => 'Demo@1234', 'password_confirmation' => 'Demo@1234']);
check('duplicate email is rejected', $dupRes['status'] === 422 && isset($dupRes['json']['errors']['email']));
$cust->refreshToken();

// ---- cart + checkout ------------------------------------------------------
$empty = $cust->post('/checkout', ['area' => 'Poblacion', 'address' => '12 Test Street', 'phone' => '0917 111 2222']);
check('checkout with empty cart fails', $empty['status'] === 422);
$cust->post('/cart/add', ['product_id' => 1]);
$cust->post('/cart/add', ['product_id' => 1]);
$added = $cust->post('/cart/add', ['product_id' => 4]);
check('cart counts 3 items', ($added['json']['count'] ?? 0) === 3, $added['body']);
$cart = $cust->request('GET', '/cart');
check('cart modal lists the dishes', str_contains($cart['body'], 'Margherita Pizza') && str_contains($cart['body'], '537.00'));
$noArea = $cust->post('/checkout', ['address' => '12 Test Street', 'phone' => '0917 111 2222']);
check('checkout needs a delivery area', $noArea['status'] === 422 && isset($noArea['json']['errors']['area']));
$placed = $cust->post('/checkout', ['area' => 'Poblacion', 'address' => '12 Test Street', 'phone' => '0917 111 2222', 'notes' => 'Ring twice']);
check('checkout creates an order', $placed['status'] === 200 && preg_match('#/orders/(\d+)$#', $placed['json']['redirect'] ?? '', $m), $placed['body']);
$orderId = (int) ($m[1] ?? 0);
$track = $cust->request('GET', "/api/orders/$orderId");
check('tracking api: pending, kitchen position', ($track['json']['status'] ?? '') === 'pending' && ($track['json']['position']['x'] ?? 0) == 12, $track['body']);
check('tracking page renders', $cust->request('GET', "/orders/$orderId", [], false)['status'] === 200);

// ---- another customer cannot see it -------------------------------------
$other = new Client($base);
$login = $other->post('/login', ['email' => 'demo@example.com', 'password' => 'Demo@1234']);
check('demo customer can log in', $login['status'] === 200, $login['body']);
check("other customer gets 404 for someone else's order", $other->request('GET', "/api/orders/$orderId")['status'] === 404);
check('wrong password is rejected', (new Client($base))->post('/login', ['email' => 'demo@example.com', 'password' => 'wrong'])['status'] === 422);
check('guests are asked to log in (401)', (new Client($base))->request('GET', "/api/orders/$orderId")['status'] === 401);

// ---- admin: customer is blocked, admin works -----------------------------
check('customers cannot open /admin (403)', $cust->request('GET', '/admin', [], false)['status'] === 403);
$admin = new Client($base);
$admin->post('/login', ['email' => 'admin@example.com', 'password' => 'Demo@1234']);
foreach (['/admin', '/admin/orders', '/admin/products', '/admin/categories', '/admin/users', '/admin/reports'] as $page) {
    $r = $admin->request('GET', $page, [], false);
    check("admin page $page", $r['status'] === 200 && !str_contains($r['body'], 'Fatal error') && !str_contains($r['body'], 'Warning:'), "HTTP {$r['status']}");
}
$skip = $admin->post("/admin/orders/$orderId/status", ['status' => 'delivered']);
check('cannot skip straight to delivered', $skip['status'] === 422);
$noRider = $admin->post("/admin/orders/$orderId/status", ['status' => 'preparing']);
check('admin starts preparing', ($noRider['json']['ok'] ?? false) === true, $noRider['body']);
$needRider = $admin->post("/admin/orders/$orderId/status", ['status' => 'out_for_delivery']);
check('sending out needs a rider', $needRider['status'] === 422 && isset($needRider['json']['errors']['rider_id']));
check('dispatch modal loads', str_contains($admin->request('GET', "/admin/orders/$orderId/dispatch")['body'], 'Rico Rider'));
$send = $admin->post("/admin/orders/$orderId/status", ['status' => 'out_for_delivery', 'rider_id' => 2]);
check('admin sends it out with rider 2', ($send['json']['ok'] ?? false) === true, $send['body']);
check('customer cannot cancel after preparing', $cust->post("/orders/$orderId/cancel")['status'] === 422);

// ---- rider ------------------------------------------------------------------
$rider = new Client($base);
$rider->post('/login', ['email' => 'rider@example.com', 'password' => 'Demo@1234']);
check('rider page loads', str_contains($rider->request('GET', '/rider', [], false)['body'], "Order #$orderId"));
check('another rider cannot post location', (new Client($base))->post("/rider/orders/$orderId/location", ['x' => 20, 'y' => 82])['status'] === 401);
$ping = $rider->post("/rider/orders/$orderId/location", ['x' => 30, 'y' => 82]);
check('rider location accepted', ($ping['json']['ok'] ?? false) === true, $ping['body']);
$pos = $cust->request('GET', "/api/orders/$orderId")['json'];
check('customer sees the rider move', ($pos['status'] ?? '') === 'out_for_delivery' && ($pos['position']['x'] ?? 0) == 30 && ($pos['rider']['name'] ?? '') === 'Rico Rider', json_encode($pos));
$done = $rider->post("/rider/orders/$orderId/location", ['x' => 70, 'y' => 82, 'arrived' => 1]);
check('arrival marks delivered', ($done['json']['delivered'] ?? false) === true, $done['body']);
$final = $cust->request('GET', "/api/orders/$orderId")['json'];
check('customer sees Delivered with 4 log rows', ($final['status'] ?? '') === 'delivered' && count($final['log'] ?? []) === 4, json_encode($final));

// ---- admin CRUD ---------------------------------------------------------------
$newDish = $admin->post('/admin/products', ['name' => 'Smoke Special', 'category_id' => 1, 'price' => '99.50', 'description' => 'test', 'emoji' => '🌭', 'is_available' => 1]);
check('create product', ($newDish['json']['ok'] ?? false) === true, $newDish['body']);
$dishId = (int) ($newDish['json']['id'] ?? 0);
check('product price validation', $admin->post('/admin/products', ['name' => 'X', 'category_id' => 1, 'price' => '-5'])['status'] === 422);
check('edit product modal loads', str_contains($admin->request('GET', "/admin/products/$dishId/edit")['body'], 'Smoke Special'));
$upd = $admin->post("/admin/products/$dishId", ['_method' => 'PUT', 'name' => 'Smoke Special 2', 'category_id' => 1, 'price' => '109']);
check('update product (PUT via _method)', ($upd['json']['ok'] ?? false) === true, $upd['body']);
$del = $admin->post("/admin/products/$dishId", ['_method' => 'DELETE']);
check('delete product (DELETE via _method)', ($del['json']['ok'] ?? false) === true, $del['body']);
$cat = $admin->post('/admin/categories', ['name' => 'Smoke Cat' . random_int(1, 9999), 'icon' => 'star']);
check('create category', ($cat['json']['ok'] ?? false) === true, $cat['body']);
check('category with dishes cannot be deleted', $admin->post('/admin/categories/1', ['_method' => 'DELETE'])['status'] === 422);
$rep = $admin->request('GET', '/admin/reports?from=2020-01-01&to=2030-01-01', [], false);
check('report shows delivered sales', $rep['status'] === 200 && str_contains($rep['body'], 'Top dishes'));

echo "\n" . ($failures === 0 ? "All checks passed.\n" : "$failures check(s) FAILED.\n");
exit($failures === 0 ? 0 : 1);
