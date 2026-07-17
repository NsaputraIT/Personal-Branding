@php $hero = \App\Models\HeroSection::first(); @endphp

<section id="hero" class="hero section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center content">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <h2>{{ $hero->heading ?? 'Crafting Digital Experiences with Passion' }}</h2>
            <p class="lead">{{ $hero->subheading ?? 'Transforming ideas into elegant solutions through creative design and innovative development' }}</p>
            <div class="cta-buttons" data-aos="fade-up" data-aos-delay="300">
              <a href="{{ $hero->cta_primary_url ?? '#portfolio' }}" class="btn btn-primary">{{ $hero->cta_primary_text ?? 'View My Work' }}</a>
              <a href="{{ $hero->cta_secondary_url ?? '#contact' }}" class="btn btn-outline">{{ $hero->cta_secondary_text ?? "Let's Connect" }}</a>
            </div>
            <div class="hero-stats" data-aos="fade-up" data-aos-delay="400">
              @foreach($hero->stats ?? [] as $stat)
              <div class="stat-item">
                <span class="stat-number">{{ $stat['number'] ?? '' }}</span>
                <span class="stat-label">{{ $stat['label'] ?? '' }}</span>
              </div>
              @endforeach
            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-image">
              <img src="{{ $hero->profile_image ? Storage::url($hero->profile_image) : asset('asset/img/preview-images-kosong.png') }}" alt="Portfolio Hero Image" class="img-fluid" data-aos="zoom-out" data-aos-delay="300">
              <div class="shape-1"></div>
              <div class="shape-2"></div>
            </div>
          </div>
        </div>

      </div>

</section>
