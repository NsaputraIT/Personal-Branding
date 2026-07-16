@php
    $footer = \App\Models\FooterSetting::first();
    $site = \App\Models\Site::first();
    $socialMedia = $site?->socialMedia ?? collect();
@endphp

<footer id="footer" class="footer">

    <div class="container">
      <div class="copyright text-center ">
        <p>{!! $footer->copyright_text ?? '&copy; <span>Copyright</span> <strong class="px-1 sitename">EasyFolio</strong> <span>All Rights Reserved</span>' !!}</p>
      </div>
      <div class="social-links d-flex justify-content-center">
        @foreach($socialMedia as $social)
        <a href="{{ $social->url ?? '#' }}"><i class="bi {{ $social->icon ?? 'bi-twitter-x' }}"></i></a>
        @endforeach
      </div>
      <div class="credits">
        {!! $footer->credit_text ?? 'Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>' !!}
      </div>
    </div>

</footer>
