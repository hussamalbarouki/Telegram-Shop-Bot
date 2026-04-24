# DEPLOYMENT_SHARED_HOSTING

## المتطلبات
- PHP 8.2+
- امتدادات: pdo_sqlite, sqlite3, mbstring, curl, openssl
- HTTPS مفعّل
- وجود ملف `.cpanel.yml` في جذر المشروع (مضاف بالفعل)
- لا توجد تغييرات غير ملتزم بها في الفرع قبل النشر

## النشر بدون SSH (رفع ZIP يدوي)
1. جهّز المشروع محليًا مع `vendor/`.
2. ارفع الملفات كاملة عبر FTP/File Manager.
3. يمكنك توجيه الدومين إلى جذر المشروع مباشرة (بسبب `index.php` الجذري) أو إلى `public/`.
4. تأكد من صلاحيات الكتابة:
   - `storage/`
   - `bootstrap/cache/`
   - `database/`
   - `public/uploads/`
5. افتح `/install` وأكمل المعالج.

## النشر عبر cPanel Git Version Control
1. اربط المستودع داخل cPanel.
2. تأكد أن `.cpanel.yml` موجود في جذر المشروع.
3. اضغط Deploy من cPanel.
4. راقب Log للتأكد أن النسخ تم إلى `public_html`.

## تشخيص خطأ 500 بعد الرفع
1. راجع `storage/logs/laravel.log`.
2. تأكد من وجود `vendor/` داخل المسار المرفوع.
3. تأكد من صلاحيات الكتابة على `storage` و`bootstrap/cache`.
4. إذا كان APP_KEY غير موجود، النظام الآن يولد pre-install key تلقائيًا لتفادي التعطل.

## بعد التثبيت
- ادخل `/admin/login`.
- اختبر `/health`.
- اضبط Webhook باستخدام السر.
