<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Setting;
use App\Support\InstallationStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallerService
{
    public function requirements(): array
    {
        $writable = [
            'storage' => is_writable(base_path('storage')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            'database' => is_writable(base_path('database')),
            'public/uploads' => is_writable(base_path('public/uploads')) || @mkdir(base_path('public/uploads'), 0775, true),
        ];

        return [
            'php' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'pdo_sqlite' => extension_loaded('pdo_sqlite'),
            'sqlite3' => extension_loaded('sqlite3'),
            'curl' => extension_loaded('curl'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'vendor' => file_exists(base_path('vendor/autoload.php')),
            'https' => request()->isSecure() || app()->environment('local'),
            'writable' => $writable,
        ];
    }

    public function install(array $data): void
    {
        $this->ensureEnv($data);
        $this->ensureSqlite();
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);

        $this->createSchema();

        DB::transaction(function () use ($data) {
            Admin::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'is_active' => true,
            ]);

            $settings = [
                'system_name' => $data['system_name'],
                'app_url' => $data['app_url'],
                'telegram_bot_token' => $data['bot_token'],
                'telegram_webhook_secret' => $data['webhook_secret'],
                'default_currency' => $data['currency'],
                'default_language' => $data['language'],
                'timezone' => $data['timezone'],
            ];

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
            }
        });

        File::put(InstallationStatus::lockFile(), now()->toDateTimeString());
    }

    private function ensureEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }

        $env = file_get_contents($envPath) ?: '';
        $pairs = [
            'APP_NAME' => '"'.$data['system_name'].'"',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => $data['app_url'],
            'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => database_path('database.sqlite'),
            'QUEUE_CONNECTION' => 'sync',
            'CACHE_STORE' => 'file',
            'SESSION_DRIVER' => 'file',
            'TELEGRAM_BOT_TOKEN' => $data['bot_token'],
            'TELEGRAM_WEBHOOK_SECRET' => $data['webhook_secret'],
            'APP_TIMEZONE' => $data['timezone'],
        ];

        foreach ($pairs as $key => $value) {
            if (preg_match('/^'.$key.'=.*/m', $env)) {
                $env = preg_replace('/^'.$key.'=.*/m', $key.'='.$value, $env);
            } else {
                $env .= PHP_EOL.$key.'='.$value;
            }
        }

        file_put_contents($envPath, trim($env).PHP_EOL);
    }

    private function ensureSqlite(): void
    {
        if (! file_exists(database_path('database.sqlite'))) {
            touch(database_path('database.sqlite'));
        }
    }

    private function createSchema(): void
    {
        Schema::dropAllTables();

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
        });

        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_id')->unique();
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('language_code', 10)->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->json('raw_json')->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) { $table->id(); $table->foreignId('parent_id')->nullable()->constrained('categories'); $table->string('name'); $table->text('description')->nullable(); $table->string('image')->nullable(); $table->integer('sort_order')->default(0); $table->boolean('is_active')->default(true); $table->timestamps(); });
        Schema::create('products', function (Blueprint $table) { $table->id(); $table->foreignId('category_id')->nullable()->constrained('categories'); $table->string('name'); $table->string('slug')->unique(); $table->string('short_description')->nullable(); $table->text('full_description')->nullable(); $table->decimal('price',12,2); $table->decimal('price_before_discount',12,2)->nullable(); $table->string('type')->default('digital_auto'); $table->string('status')->default('active'); $table->integer('stock')->default(0); $table->string('sku')->nullable(); $table->integer('sort_order')->default(0); $table->boolean('is_featured')->default(false); $table->json('sizes')->nullable(); $table->json('colors')->nullable(); $table->string('material')->nullable(); $table->string('weight')->nullable(); $table->string('expected_delivery_time')->nullable(); $table->text('notes_before_purchase')->nullable(); $table->integer('min_qty')->default(1); $table->integer('max_qty')->default(1); $table->boolean('requires_address')->default(false); $table->boolean('requires_phone')->default(false); $table->boolean('requires_note')->default(false); $table->timestamps(); });
        Schema::create('product_images', function (Blueprint $table) { $table->id(); $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); $table->string('path'); $table->string('telegram_file_id')->nullable(); $table->boolean('is_primary')->default(false); $table->timestamps(); });
        Schema::create('product_variants', function (Blueprint $table) { $table->id(); $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); $table->string('size')->nullable(); $table->string('color')->nullable(); $table->decimal('extra_price',12,2)->default(0); $table->integer('stock')->default(0); $table->timestamps(); });
        Schema::create('digital_delivery_items', function (Blueprint $table) { $table->id(); $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); $table->longText('content'); $table->string('file_path')->nullable(); $table->string('status')->default('available'); $table->foreignId('delivered_to_user_id')->nullable()->constrained('telegram_users'); $table->foreignId('delivered_order_id')->nullable()->constrained('orders'); $table->timestamp('delivered_at')->nullable(); $table->timestamps(); });
        Schema::create('orders', function (Blueprint $table) { $table->id(); $table->foreignId('telegram_user_id')->constrained('telegram_users'); $table->string('status')->default('pending_payment'); $table->decimal('subtotal',12,2)->default(0); $table->decimal('discount_total',12,2)->default(0); $table->decimal('total',12,2)->default(0); $table->string('currency',10)->default('USD'); $table->text('address')->nullable(); $table->string('phone_number')->nullable(); $table->text('customer_note')->nullable(); $table->text('admin_note')->nullable(); $table->string('tracking_number')->nullable(); $table->text('customer_message')->nullable(); $table->timestamp('paid_at')->nullable(); $table->timestamps(); });
        Schema::create('order_items', function (Blueprint $table) { $table->id(); $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete(); $table->foreignId('product_id')->constrained('products'); $table->integer('qty'); $table->decimal('unit_price',12,2); $table->decimal('total_price',12,2); $table->json('variant_options')->nullable(); $table->timestamps(); });
        Schema::create('wallets', function (Blueprint $table) { $table->id(); $table->foreignId('telegram_user_id')->unique()->constrained('telegram_users')->cascadeOnDelete(); $table->decimal('balance',12,2)->default(0); $table->string('currency',10)->default('USD'); $table->timestamps(); });
        Schema::create('wallet_transactions', function (Blueprint $table) { $table->id(); $table->foreignId('telegram_user_id')->constrained('telegram_users')->cascadeOnDelete(); $table->string('type'); $table->decimal('amount',12,2); $table->decimal('balance_before',12,2); $table->decimal('balance_after',12,2); $table->string('reference_type')->nullable(); $table->unsignedBigInteger('reference_id')->nullable(); $table->text('note')->nullable(); $table->foreignId('created_by')->nullable()->constrained('admins'); $table->timestamps(); });
        Schema::create('deposit_codes', function (Blueprint $table) { $table->id(); $table->string('code')->unique(); $table->decimal('amount',12,2); $table->integer('max_uses')->default(1); $table->integer('used_count')->default(0); $table->timestamp('expires_at')->nullable(); $table->string('status')->default('active'); $table->foreignId('created_by')->nullable()->constrained('admins'); $table->timestamps(); });
        Schema::create('coupon_codes', function (Blueprint $table) { $table->id(); $table->string('code')->unique(); $table->string('discount_type'); $table->decimal('discount_value',12,2); $table->timestamp('expires_at')->nullable(); $table->integer('usage_limit')->nullable(); $table->integer('used_count')->default(0); $table->json('product_ids')->nullable(); $table->json('category_ids')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); });
        Schema::create('notifications', function (Blueprint $table) { $table->id(); $table->foreignId('telegram_user_id')->nullable()->constrained('telegram_users'); $table->string('type'); $table->longText('message'); $table->string('image')->nullable(); $table->string('status')->default('queued'); $table->timestamps(); });
        Schema::create('broadcasts', function (Blueprint $table) { $table->id(); $table->string('target_group'); $table->longText('message'); $table->string('image')->nullable(); $table->string('status')->default('draft'); $table->timestamps(); });
        Schema::create('broadcast_recipients', function (Blueprint $table) { $table->id(); $table->foreignId('broadcast_id')->constrained('broadcasts')->cascadeOnDelete(); $table->foreignId('telegram_user_id')->constrained('telegram_users')->cascadeOnDelete(); $table->string('status')->default('pending'); $table->timestamps(); });
        Schema::create('support_tickets', function (Blueprint $table) { $table->id(); $table->foreignId('telegram_user_id')->constrained('telegram_users')->cascadeOnDelete(); $table->string('subject')->nullable(); $table->string('status')->default('open'); $table->timestamps(); });
        Schema::create('support_messages', function (Blueprint $table) { $table->id(); $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete(); $table->string('sender_type'); $table->unsignedBigInteger('sender_id')->nullable(); $table->text('message'); $table->timestamps(); });
        Schema::create('webhook_logs', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('telegram_update_id')->nullable(); $table->json('payload')->nullable(); $table->string('status')->default('received'); $table->text('error_message')->nullable(); $table->timestamps(); });
        Schema::create('admin_activity_logs', function (Blueprint $table) { $table->id(); $table->foreignId('admin_id')->nullable()->constrained('admins'); $table->string('action'); $table->json('meta')->nullable(); $table->timestamps(); });
        Schema::create('failed_actions', function (Blueprint $table) { $table->id(); $table->string('type'); $table->json('payload')->nullable(); $table->text('error_message')->nullable(); $table->timestamps(); });
    }
}
