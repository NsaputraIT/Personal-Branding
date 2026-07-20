@php
    $footer = \App\Models\FooterSetting::first();
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

<footer id="footer" class="footer">

    <div class="container">
      <div class="copyright text-center ">
        <p>{!! $footer->copyright_text ?? '&copy; <span>Copyright</span> <strong class="px-1 sitename">Personal Branding</strong> <span>All Rights Reserved</span>' !!}</p>
      </div>
      <div class="social-links d-flex justify-content-center">
        @foreach($socialMedia as $social)
        <a href="{{ $social->medsos_url ?? '#' }}"><i class="{{ $platformMap[$social->medsos_icon]['icon'] ?? 'bi-twitter-x' }}"></i></a>
        @endforeach
      </div>
      <div class="credits">
        {!! $footer->credit_text ?? 'Crafted with &hearts; by Personal Branding' !!}
      </div>
    </div>

</footer>
