@php $about = \App\Models\AboutSection::first(); @endphp

<section id="about" class="about section light-background">

      <div class="container section-title" data-aos="fade-up">
        <h2>{{ $about->heading ?? 'About' }}</h2>
        <div class="title-shape">
          <svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path>
          </svg>
        </div>
        <p>{{ $about->description ?? 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem' }}</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">
          <div class="col-lg-6 position-relative" data-aos="fade-right" data-aos-delay="200">
            <div class="about-image">
              <img src="{{ asset($about->profile_image ?? 'asset/img/profile/profile-square-2.webp') }}" alt="Profile Image" class="img-fluid rounded-4">
            </div>
          </div>

          <div class="col-lg-6" data-aos="fade-left" data-aos-delay="300">
            <div class="about-content">
              <span class="subtitle">{{ $about->subtitle ?? 'About Me' }}</span>

              <h2>{{ $about->heading ?? 'UI/UX Designer & Web Developer' }}</h2>

              <p class="lead mb-4">{{ $about->paragraph1 ?? 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.' }}</p>

              <p class="mb-4">{{ $about->paragraph2 ?? 'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet' }}</p>

              <div class="personal-info">
                <div class="row g-4">
                  @foreach($about->info_items ?? [] as $item)
                  <div class="col-6">
                    <div class="info-item">
                      <span class="label">{{ $item['label'] ?? '' }}</span>
                      <span class="value">{{ $item['value'] ?? '' }}</span>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>

              <div class="signature mt-4">
                <div class="signature-image">
                  <img src="{{ asset($about->signature_image ?? 'asset/img/misc/signature-1.webp') }}" alt="" class="img-fluid">
                </div>
                <div class="signature-info">
                  <h4>{{ $about->signature_name ?? 'Eliot Johnson' }}</h4>
                  <p>{{ $about->signature_title ?? 'Adipiscing Elit, Lorem Ipsum' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

</section>
