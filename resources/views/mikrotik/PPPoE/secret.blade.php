@extends('Template.utama')

@section('title', 'PPPoE Secret')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Selamat Datang di Dashboard</h1>
    <p>Hello World.</p>
@endsection

@push('scripts')
<script>
    console.log("Dashboard loaded!");
</script>
@endpush
