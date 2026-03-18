@extends('layouts.dashboard')

@section('title', isset($orderId) ? 'Modifier Commande' : 'Nouvelle Commande')

@section('content')
    @livewire('orders.create-order', ['orderId' => $orderId ?? null])
@endsection