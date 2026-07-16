@php $contact = \App\Models\ContactInfo::first(); @endphp

<section id="contact" class="contact section light-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-5">
          <div class="col-lg-6">
            <div class="content" data-aos="fade-up" data-aos-delay="200">
              <div class="section-category mb-3">{{ $contact->heading ?? 'Contact' }}</div>
              <h2 class="display-5 mb-4">{{ $contact->subheading ?? 'Nemo enim ipsam voluptatem quia voluptas aspernatur' }}</h2>
              <p class="lead mb-4">{{ $contact->description ?? 'Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam.' }}</p>

              <div class="contact-info mt-5">
                <div class="info-item d-flex mb-3">
                  <i class="bi bi-envelope-at me-3"></i>
                  <span>{{ $contact->email ?? 'info@example.com' }}</span>
                </div>

                <div class="info-item d-flex mb-3">
                  <i class="bi bi-telephone me-3"></i>
                  <span>{{ $contact->phone ?? '+1 5589 55488 558' }}</span>
                </div>

                <div class="info-item d-flex mb-4">
                  <i class="bi bi-geo-alt me-3"></i>
                  <span>{{ $contact->address ?? 'A108 Adam Street, New York, NY 535022' }}</span>
                </div>

                <a href="{{ $contact->map_url ?? '#' }}" class="map-link d-inline-flex align-items-center">
                  {{ $contact->map_text ?? 'Open Map' }}
                  <i class="bi bi-arrow-right ms-2"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="contact-form card" data-aos="fade-up" data-aos-delay="300">
              <div class="card-body p-4 p-lg-5">

                <form action="#" method="post" class="php-email-form">
                  <div class="row gy-4">

                    <div class="col-12">
                      <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
                    </div>

                    <div class="col-12 ">
                      <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
                    </div>

                    <div class="col-12">
                      <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
                    </div>

                    <div class="col-12">
                      <textarea class="form-control" name="message" rows="6" placeholder="Message" required=""></textarea>
                    </div>

                    <div class="col-12 text-center">
                      <div class="loading">Loading</div>
                      <div class="error-message"></div>
                      <div class="sent-message">Your message has been sent. Thank you!</div>

                      <button type="submit" class="btn btn-submit w-100">Submit Message</button>
                    </div>

                  </div>
                </form>

              </div>
            </div>
          </div>

        </div>

      </div>

</section>
