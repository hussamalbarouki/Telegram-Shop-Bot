@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>لوحة التحكم</h3>
    <form method="post" action="/admin/logout">@csrf<button class="btn btn-outline-danger">خروج</button></form>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3">المستخدمون: {{ $usersCount }}</div></div>
    <div class="col-md-3"><div class="card p-3">طلبات اليوم: {{ $todayOrders }}</div></div>
    <div class="col-md-3"><div class="card p-3">المبيعات: {{ number_format($sales,2) }}</div></div>
    <div class="col-md-3"><div class="card p-3">منخفض المخزون: {{ $lowStock }}</div></div>
</div>
<div class="card p-3">
    <h5>آخر الطلبات</h5>
    <table class="table table-sm">
        <thead><tr><th>#</th><th>الحالة</th><th>الإجمالي</th><th>تاريخ</th></tr></thead>
        <tbody>
        @foreach($recentOrders as $order)
            <tr><td>{{ $order->id }}</td><td>{{ $order->status }}</td><td>{{ $order->total }}</td><td>{{ $order->created_at }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
