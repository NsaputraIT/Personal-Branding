@php
    $services = \App\Models\Service::where('is_active', true)->orderBy('sort_order')->get();
    $site = \App\Models\Site::first();
    $meta = $site?->section_metadata;
    $sectionData = $meta['services'] ?? [];
@endphp

<section id="services" class="services section">

      <div class="container section-title" data-aos="fade-up">
        <h2>{{ $sectionData['title'] ?? 'Services' }}</h2>
        <div class="title-shape">
          <svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path>
          </svg>
        </div>
        <p>{{ $sectionData['description'] ?? 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem' }}</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">
          <div class="col-lg-4 mb-5 mb-lg-0">
            <h2 class="fw-bold mb-4 servies-title">{{ $sectionData['sidebar_heading'] ?? 'Consectetur adipiscing elit sed do eiusmod tempor' }}</h2>
            <p class="mb-4">{{ $sectionData['sidebar_text'] ?? 'Nulla metus metus ullamcorper vel tincidunt sed euismod nibh volutpat velit class aptent taciti sociosqu ad litora.' }}</p>
            <a href="#" class="btn btn-outline-primary">{{ $sectionData['sidebar_cta'] ?? 'See all services' }}</a>
          </div>
          <div class="col-lg-8">
            <div class="row g-4">

              @foreach($services as $index => $service)
              <div class="col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 + 100 }}">
                <div class="service-item">
                  <i class="bi {{ $service->icon ?? 'bi-activity' }} icon"></i>
                  <h3><a href="{{ route('portfolio.service-details') }}">{{ $service->title }}</a></h3>
                  <p>{{ $service->description }}</p>
                </div>
              </div>
              @endforeach

            </div>
          </div>
        </div>

      </div>

</section>
