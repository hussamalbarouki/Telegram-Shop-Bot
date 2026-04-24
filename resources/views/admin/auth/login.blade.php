@extends('layouts.app')
@section('content')
<div class="row justify-content-center"><div class="col-md-5"><div class="card p-4">
<h4>تسجيل دخول الإدارة</h4>
<form method="post" action="/admin/login" class="mt-3">@csrf
<div class="mb-3"><label>البريد</label><input name="email" class="form-control"></div>
<div class="mb-3"><label>كلمة المرور</label><input type="password" name="password" class="form-control"></div>
<button class="btn btn-dark w-100">دخول</button>
</form>
</div></div></div>
@endsection
