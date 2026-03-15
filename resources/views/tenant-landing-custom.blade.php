@extends('layouts.client')

@section('content')
    @include('tenants.templates.' . ($tenant->landing_config['template'] ?? 'nature'))
@endsection
