# SECURITY

- كلمات مرور المدير hashed عبر `Hash::make`.
- حماية CSRF مفعلة في لوحة الإدارة.
- تحقق صارم للمدخلات داخل Controller.
- حماية webhook عبر secret في URL + Header.
- رفض الوصول للملفات الحساسة عبر `.htaccess`.
- عدم الاعتماد على Redis/Supervisor/Queue worker دائم.
- حفظ الملفات الحساسة الرقمية في `storage/app/digital-deliveries`.
- التسجيل في `webhook_logs` و `admin_activity_logs` و `failed_actions`.
