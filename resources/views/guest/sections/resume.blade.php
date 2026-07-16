@php
    $workEntries = \App\Models\ResumeEntry::where('type', 'work')->where('is_active', true)->orderBy('sort_order')->get();
    $educationEntries = \App\Models\ResumeEntry::where('type', 'education')->where('is_active', true)->orderBy('sort_order')->get();
    $site = \App\Models\Site::first();
    $meta = $site?->section_metadata;
    $sectionData = $meta['resume'] ?? [];
@endphp

<section id="resume" class="resume section">

      <div class="container section-title" data-aos="fade-up">
        <h2>{{ $sectionData['title'] ?? 'Resume' }}</h2>
        <div class="title-shape">
          <svg viewBox="0 0 200 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0,10 C 40,0 60,20 100,10 C 140,0 160,20 200,10" fill="none" stroke="currentColor" stroke-width="2"></path>
          </svg>
        </div>
        <p>{{ $sectionData['description'] ?? 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur vel illum qui dolorem' }}</p>
      </div>

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
          <div class="col-12">
            <div class="resume-wrapper">
              <div class="resume-block" data-aos="fade-up">
                <h2>{{ $sectionData['work_title'] ?? 'Work Experience' }}</h2>
                <p class="lead">{{ $sectionData['work_description'] ?? 'Maecenas tempus tellus eget condimentum rhoncus sem quam semper libero sit amet adipiscing sem neque sed ipsum.' }}</p>

                <div class="timeline">
                  @foreach($workEntries as $index => $entry)
                  <div class="timeline-item" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 + 100 }}">
                    <div class="timeline-left">
                      <h4 class="company">{{ $entry->company }}</h4>
                      <span class="period">{{ $entry->period }}</span>
                    </div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-right">
                      <h3 class="position">{{ $entry->title }}</h3>
                      <p class="description">{{ $entry->description }}</p>
                      @if($entry->bullets && count($entry->bullets) > 0)
                      <ul>
                        @foreach($entry->bullets as $bullet)
                        <li>{{ $bullet }}</li>
                        @endforeach
                      </ul>
                      @endif
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>

              <div class="resume-block" data-aos="fade-up" data-aos-delay="100">
                <h2>{{ $sectionData['education_title'] ?? 'My Education' }}</h2>
                <p class="lead">{{ $sectionData['education_description'] ?? 'Maecenas tempus tellus eget condimentum rhoncus sem quam semper libero sit amet adipiscing.' }}</p>

                <div class="timeline">
                  @foreach($educationEntries as $index => $entry)
                  <div class="timeline-item" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 + 100 }}">
                    <div class="timeline-left">
                      <h4 class="company">{{ $entry->company }}</h4>
                      <span class="period">{{ $entry->period }}</span>
                    </div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-right">
                      <h3 class="position">{{ $entry->title }}</h3>
                      <p class="description">{{ $entry->description }}</p>
                      @if($entry->bullets && count($entry->bullets) > 0)
                      <ul>
                        @foreach($entry->bullets as $bullet)
                        <li>{{ $bullet }}</li>
                        @endforeach
                      </ul>
                      @endif
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

</section>
