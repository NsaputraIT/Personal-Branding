@php
    $site = \App\Models\Site::first();
    $socialMedia = $site?->socialMedia ?? collect();

    $platformMap = [
        'twitter'   => ['name' => 'Twitter/X',   'icon' => 'bi-twitter-x'],
        'facebook'  => ['name' => 'Facebook',     'icon' => 'bi-facebook'],
        'instagram' => ['name' => 'Instagram',    'icon' => 'bi-instagram'],
        'linkedin'  => ['name' => 'LinkedIn',     'icon' => 'bi-linkedin'],
        'tiktok'    => ['name' => 'TikTok',       'icon' => 'bi-tiktok'],
        'blog'      => ['name' => 'Blog',         'icon' => 'bi-pencil-square'],
    ];
@endphp

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="{{ route('portfolio.home') }}" class="logo d-flex align-items-center me-auto me-xl-0">
        <h1 class="sitename">{{ $site?->site_name ?? 'Indra Paradana' }}</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#resume">Resume</a></li>
          <li><a href="#portfolio">Portfolio</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
</header>
