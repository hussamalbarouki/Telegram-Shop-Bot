@extends('layouts.app')
@section('content')
<div class="card p-4">
    <h3 class="mb-3">تثبيت النظام</h3>
    <div class="row mb-4">
        @foreach($requirements as $key => $value)
            @if($key === 'writable')
                @foreach($value as $dir => $ok)
                    <div class="col-md-3"><small>{{ $dir }}: {!! $ok ? '✅' : '❌' !!}</small></div>
                @endforeach
            @else
                <div class="col-md-3"><small>{{ $key }}: {!! $value ? '✅' : '❌' !!}</small></div>
            @endif
        @endforeach
    </div>

    <form method="post" action="/install" class="row g-3">
        @csrf
        <div class="col-md-6"><label>اسم النظام</label><input name="system_name" class="form-control" value="Telegram Shop"></div>
        <div class="col-md-6"><label>APP_URL</label><input name="app_url" class="form-control" placeholder="https://example.com"></div>
        <div class="col-md-6"><label>Bot Token</label><input name="bot_token" class="form-control"></div>
        <div class="col-md-6"><label>Webhook Secret</label><input name="webhook_secret" class="form-control" value="{{ \Illuminate\Support\Str::random(40) }}"></div>
        <div class="col-md-4"><label>اسم المدير</label><input name="admin_name" class="form-control"></div>
        <div class="col-md-4"><label>بريد المدير</label><input name="admin_email" class="form-control"></div>
        <div class="col-md-4"><label>كلمة المرور</label><input type="password" name="admin_password" class="form-control"></div>
        <div class="col-md-4"><label>العملة</label><input name="currency" class="form-control" value="USD"></div>
        <div class="col-md-4"><label>اللغة</label><input name="language" class="form-control" value="ar"></div>
        <div class="col-md-4"><label>Timezone</label><input name="timezone" class="form-control" value="Asia/Riyadh"></div>
        <div class="col-12"><button class="btn btn-primary">تثبيت الآن</button></div>
    </form>
</div>
@endsection
