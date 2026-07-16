@php
    $testimonials = \App\Models\Testimonial::where('is_active', true)->orderBy('sort_order')->get();
    $site = \App\Models\Site::first();
    $meta = $site?->section_metadata;
    $sectionData = $meta['testimonials'] ?? [];
@endphp

<section id="testimonials" class="testimonials section light-background">

      <div class="container section-title" data-aos="fade-up">
        <h2>{{ $sectionData['title'] ?? 'Testimonials' }}</h2>
        <div class="title-shape">
          <svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path>
          </svg>
        </div>
        <p>{{ $sectionData['description'] ?? 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem' }}</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="testimonials-slider swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "slidesPerView": 1,
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "navigation": {
                "nextEl": ".swiper-button-next",
                "prevEl": ".swiper-button-prev"
              }
            }
          </script>

          <div class="swiper-wrapper">

            @foreach($testimonials as $testimonial)
            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="row">
                  <div class="col-lg-8">
                    <h2>{{ $testimonial->title }}</h2>
                    <p>
                      {{ $testimonial->quote }}
                    </p>
                    @if($testimonial->quote_extra)
                    <p>
                      {{ $testimonial->quote_extra }}
                    </p>
                    @endif
                    <div class="profile d-flex align-items-center">
                      <img src="{{ asset($testimonial->avatar ?? 'asset/img/person/person-m-7.webp') }}" class="profile-img" alt="">
                      <div class="profile-info">
                        <h3>{{ $testimonial->name }}</h3>
                        <span>{{ $testimonial->role }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 d-none d-lg-block">
                    <div class="featured-img-wrapper">
                      <img src="{{ asset($testimonial->avatar ?? 'asset/img/person/person-m-7.webp') }}" class="featured-img" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endforeach

          </div>

          <div class="swiper-navigation w-100 d-flex align-items-center justify-content-center">
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
          </div>

        </div>

      </div>

</section>
