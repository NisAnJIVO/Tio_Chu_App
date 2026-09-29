<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TODAS LAS BAR_SALES SESION 4 (Domingo 27) - Barra Kelly ===\n";
$sales = App\Models\BarSale::with('product')
    ->where('night_session_id', 4)
    ->where('bar_name', 'Barra Kelly (Principal)')
    ->get();

foreach($sales as $s) {
    $name = $s->product ? $s->product->name : 'N/A';
    $cat = $s->product ? $s->product->category : 'N/A';
    echo "[ID:{$s->id}] {$name} ({$cat}) | pkg:{$s->packages} u:{$s->units} | tot_init:{$s->total_initial} | saldo:{$s->saldo} | vendido:{$s->vendido} | subtotal:{$s->subtotal}\n";
}

echo "\n=== CHECKING PUT ROUTE ===\n";
// Check if 405 or similar would happen
$router = app('router');
$routes = collect($router->getRoutes()->getRoutes());
foreach($routes as $route) {
    if (str_contains($route->uri(), 'bulk-update') || str_contains($route->getName() ?? '', 'updateBulk')) {
        echo "Route: [{$route->methods()[0]}] {$route->uri()} => {$route->getName()}\n";
    }
}
