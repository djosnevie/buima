@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    @if(auth()->user()->isSuperAdmin())
        <livewire:admin.dashboard.super-admin-dashboard />
    @else
        @livewire('dashboard-stats')

        <div class="row g-4">
            <div class="col-md-8">
                @livewire('order-chart')
            </div>
            <div class="col-md-4">
                @livewire('top-products')
            </div>
        </div>
    @endif
@endsection