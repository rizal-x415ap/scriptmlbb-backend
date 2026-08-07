<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PremiumToken;
use Illuminate\Support\Carbon;

PremiumToken::query()->update([
    'device_fingerprint' => null,
    'device_name' => null,
    'activated_at' => null,
    'is_active' => true,
    'expires_at' => Carbon::now()->addDays(30),
]);

echo "SUCCESS: Reset device fingerprint for all premium tokens!\n";
