@extends('guest.layouts.app')

@section('title', 'Home - Indra Pradana')
@section('meta_description', 'Crafting Digital Experiences with Passion — Transforming ideas into elegant solutions through creative design and innovative development.')
@section('meta_keywords', 'portfolio, web design, UI/UX, developer, creative')
@section('body_class', 'index-page')

@section('content')

    @include('guest.sections.hero')
    @include('guest.sections.about')
    @include('guest.sections.skills')
    @include('guest.sections.resume')
    @include('guest.sections.portfolio')
    @include('guest.sections.testimonials')
    @include('guest.sections.services')
    @include('guest.sections.faq')
    @include('guest.sections.contact')

@endsection
