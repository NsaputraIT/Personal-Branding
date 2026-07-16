@php
    $faqItems = \App\Models\FaqItem::where('is_active', true)->orderBy('sort_order')->get();
    $site = \App\Models\Site::first();
    $meta = $site?->section_metadata;
    $sectionData = $meta['faq'] ?? [];
@endphp

<section id="faq" class="faq section">

      <div class="container section-title" data-aos="fade-up">
        <h2>{{ $sectionData['title'] ?? 'Frequently Asked Questions' }}</h2>
        <div class="title-shape">
          <svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path>
          </svg>
        </div>
        <p>{{ $sectionData['description'] ?? 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem' }}</p>
      </div>

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

            <div class="faq-container">

              @foreach($faqItems as $index => $item)
              <div class="faq-item{{ $index === 0 ? ' faq-active' : '' }}">
                <h3>{{ $item->question }}</h3>
                <div class="faq-content">
                  <p>{{ $item->answer }}</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>
              @endforeach

            </div>

          </div>

        </div>

      </div>

</section>
