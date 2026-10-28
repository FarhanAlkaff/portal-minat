@extends('layouts.public')
@section('title', $title)
@section('content')
<div class="container py-5">
    <h1 class="section-title mb-4">{{ $title }}</h1>
    <div class="p-4 rounded-4 bg-white shadow-sm" style="white-space:pre-line;line-height:1.75;">{{ $content }}</div>
    <a href="{{ route('landing') }}" class="btn btn-azhar mt-3">← Kembali ke Beranda</a>
</div>
@endsection
