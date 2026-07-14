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
          <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <div class="header-social-links">
        @foreach ($socialMedia as $sm)
            <a href="{{ $sm->medsos_url ?: '#' }}" class="{{ $sm->medsos_icon }}">
                <i class="{{ $platformMap[$sm->medsos_icon]['icon'] ?? 'bi-link-45deg' }}"></i>
            </a>
        @endforeach
      </div>

    </div>
</header>
