# Telegram Shop Bot (Laravel 11)

نظام متجر Telegram Bot جاهز للاستضافة المشتركة بدون SSH/Root بعد الرفع.

## المزايا الأساسية
- Web Installer على `/install` بدون أوامر Artisan على السيرفر.
- قاعدة بيانات SQLite تلقائيًا.
- Webhook endpoint: `POST /telegram/webhook/{secret}`.
- لوحة إدارة RTL (Bootstrap 5) مع تسجيل دخول مدير.
- نظام مستخدمي تيليغرام + محفظة + طلبات + تسليم رقمي.
- Health check عبر `/health` أو `public/health.php`.
- ملف `index.php` في الجذر لتجنب خطأ 500 عند توجيه الدومين إلى جذر المشروع مباشرة.

## التشغيل المحلي
1. `composer install`
2. `cp .env.example .env`
3. شغل محليًا: `php artisan serve`
4. افتح `http://127.0.0.1:8000/install`

> ملاحظة: التثبيت يتم من المتصفح. لا حاجة لـ `php artisan migrate` في بيئة الاستضافة.

## تجهيز ZIP للاستضافة المشتركة
1. على جهازك المحلي نفّذ:
   - `composer install --optimize-autoloader --no-dev`
2. تأكد أن `vendor/` موجود داخل المشروع.
3. اضغط المشروع ZIP كاملًا وارفعه عبر FTP/File Manager.
4. إن كان الدومين يشير إلى جذر المشروع فسيعمل عبر `index.php` الجذري تلقائيًا.

## التثبيت من المتصفح
1. افتح `https://your-domain.com/install`.
2. أدخل بيانات النظام والبوت والمدير.
3. بعد النجاح ستنتقل إلى `/admin/login`.
4. سيتم إنشاء ملف `storage/installed.lock` ومنع إعادة التثبيت.

## حل خطأ 500 الشائع بعد الرفع
- السبب الأكثر شيوعًا: عدم وجود `APP_KEY` قبل أول تشغيل.
- تم حل ذلك برمجيًا عبر مفتاح pre-install تلقائي داخل `AppServiceProvider`.
- تأكد من صلاحيات الكتابة على:
  - `storage/`
  - `bootstrap/cache/`
  - `database/`
  - `public/uploads/`

## ضبط Webhook
صيغة الرابط:
`https://your-domain.com/telegram/webhook/{secret}`

من لوحة الإدارة أو API Telegram استخدم نفس `secret`.

## النسخ الاحتياطي
- نسخة SQLite: `database/database.sqlite`.
- الملفات العامة: `public/uploads`.
- التسليمات الرقمية الحساسة: `storage/app/digital-deliveries`.
