@php
    $items = \App\Models\PortfolioItem::where('is_active', true)->orderBy('sort_order')->get();
    $categories = $items->pluck('category')->unique();
    $site = \App\Models\Site::first();
    $meta = $site?->section_metadata;
    $sectionData = $meta['portfolio'] ?? [];
@endphp

<section id="portfolio" class="portfolio section">

      <div class="container section-title" data-aos="fade-up">
        <h2>{{ $sectionData['title'] ?? 'Portfolio' }}</h2>
        <div class="title-shape">
          <svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path>
          </svg>
        </div>
        <p>{{ $sectionData['description'] ?? 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem' }}</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

          <div class="portfolio-filters-container" data-aos="fade-up" data-aos-delay="200">
            <ul class="portfolio-filters isotope-filters">
              <li data-filter="*" class="filter-active">{{ $sectionData['all_text'] ?? 'All Work' }}</li>
              @foreach($categories as $category)
              <li data-filter=".filter-{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</li>
              @endforeach
            </ul>
          </div>

          <div class="row g-4 isotope-container" data-aos="fade-up" data-aos-delay="300">

            @foreach($items as $item)
            <div class="col-lg-6 col-md-6 portfolio-item isotope-item filter-{{ \Illuminate\Support\Str::slug($item->category) }}">
              <div class="portfolio-card">
                <div class="portfolio-image">
                  <img src="{{ asset($item->image ?? 'asset/img/portfolio/portfolio-1.webp') }}" class="img-fluid" alt="" loading="lazy">
                  <div class="portfolio-overlay">
                    <div class="portfolio-actions">
                      <a href="{{ asset($item->image ?? 'asset/img/portfolio/portfolio-1.webp') }}" class="glightbox preview-link" data-gallery="portfolio-gallery-{{ \Illuminate\Support\Str::slug($item->category) }}"><i class="bi bi-eye"></i></a>
                      <a href="{{ $item->url ? route('portfolio.details', $item->url) : route('portfolio.details') }}" class="details-link"><i class="bi bi-arrow-right"></i></a>
                    </div>
                  </div>
                </div>
                <div class="portfolio-content">
                  <span class="category">{{ $item->category }}</span>
                  <h3>{{ $item->title }}</h3>
                  <p>{{ $item->description }}</p>
                </div>
              </div>
            </div>
            @endforeach

          </div>

        </div>

      </div>

</section>
