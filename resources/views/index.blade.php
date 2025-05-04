@extends('Template.utama')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Selamat Datang di Dashboard</h1>
    <p>Ini adalah halaman utama aplikasi kamu.</p>
@endsection

@push('scripts')
<script>
    console.log("Dashboard loaded!");
</script>
@endpush
