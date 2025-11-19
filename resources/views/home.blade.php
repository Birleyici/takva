@extends('layouts.app')

@section('title', 'Ana Sayfa - Takva Dergisi')
@section('description', 'Takva Dergisi - İslami bilim, kültür ve düşünce dünyasından en güncel makaleler ve sayılar')
@section('keywords', 'takva, dergi, islam, makaleler, bilim, kültür')

@section('og_title', 'Ana Sayfa - Takva Dergisi')
@section('og_description', 'İslami bilim, kültür ve düşünce dünyasından en güncel makaleler ve sayılar')
@section('og_type', 'website')

@section('twitter_title', 'Ana Sayfa - Takva Dergisi')
@section('twitter_description', 'İslami bilim, kültür ve düşünce dünyasından en güncel makaleler ve sayılar')

@section('content')
    <x-sections.hero :latest-issue="$latestIssue" />

    <x-sections.latest-articles
        :latest-articles="$latestArticles"
        :popular-articles="$popularArticles" />

    <x-sections.latest-issues :issues="$latestIssues" />

    <x-sections.newsletter />
@endsection
