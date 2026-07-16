@php
    $skills = \App\Models\Skill::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
    $site = \App\Models\Site::first();
    $meta = $site?->section_metadata;
    $sectionData = $meta['skills'] ?? [];
@endphp

<section id="skills" class="skills section">

      <div class="container section-title" data-aos="fade-up">
        <h2>{{ $sectionData['title'] ?? 'Skills' }}</h2>
        <div class="title-shape">
          <svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path>
          </svg>
        </div>
        <p>{{ $sectionData['description'] ?? 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem' }}</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4 skills-animation">

          @foreach($skills as $index => $skill)
          <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
            <div class="skill-box">
              <h3>{{ $skill->name }}</h3>
              <p>{{ $skill->description }}</p>
              <span class="text-end d-block">{{ $skill->percentage }}%</span>
              <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="{{ $skill->percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
          @endforeach

        </div>

      </div>

</section>
