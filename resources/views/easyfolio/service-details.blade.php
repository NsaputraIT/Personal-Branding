@extends('layouts.public')

@section('title', 'Service Details - EasyFolio')
@section('meta_description', 'Detailed view of our services and offerings.')
@section('meta_keywords', 'services, web design, development, consulting')
@section('body_class', 'service-details-page')

@section('content')

    <!-- Page Title -->
    <div class="page-title">
      <div class="breadcrumbs">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('portfolio.home') }}"><i class="bi bi-house"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('portfolio.home') }}#services">Category</a></li>
            <li class="breadcrumb-item active current">Service Details</li>
          </ol>
        </nav>
      </div>

      <div class="title-wrapper">
        <h1>Service Details</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>
      </div>
    </div><!-- End Page Title -->

    <!-- Service Details Section -->
    <section id="service-details" class="service-details section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-8">
            <div class="service-details-slider swiper init-swiper">
              <script type="application/json" class="swiper-config">
                {
                  "loop": true,
                  "speed": 600,
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": 1,
                  "pagination": {
                    "el": ".swiper-pagination",
                    "type": "bullets",
                    "clickable": true
                  }
                }
              </script>
              <div class="swiper-wrapper align-items-center">
                <div class="swiper-slide">
                  <img src="{{ asset('asset/img/services/services-1.webp') }}" alt="" class="img-fluid" loading="lazy">
                </div>
                <div class="swiper-slide">
                  <img src="{{ asset('asset/img/services/services-2.webp') }}" alt="" class="img-fluid" loading="lazy">
                </div>
                <div class="swiper-slide">
                  <img src="{{ asset('asset/img/services/services-3.webp') }}" alt="" class="img-fluid" loading="lazy">
                </div>
              </div>
              <div class="swiper-pagination"></div>
            </div>

            <div class="content mt-5">
              <h3>Digital Marketing Solutions</h3>
              <p>
                Qui laudantium consequatur laborum sit qui ad sapiente dila parde sonata raqer a videna mareta paulona marka. Aut quiatem. Ut earum tempore quidem qui recusandae distinctio quo.
              </p>
              <p>
                Et officiis id est ad voluptates sint quia architecto aut soluta eum voluptatum rerum illo mara. Ut earum tempore quidem qui recusandae distinctio quo. Veniam maiores eos cumque distinctio.
              </p>

              <div class="features mt-4">
                <div class="row gy-4">
                  <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-box d-flex align-items-center">
                      <i class="bi bi-check"></i>
                      <h4>Search Engine Optimization</h4>
                    </div>
                  </div>
                  <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-box d-flex align-items-center">
                      <i class="bi bi-check"></i>
                      <h4>Social Media Marketing</h4>
                    </div>
                  </div>
                  <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-box d-flex align-items-center">
                      <i class="bi bi-check"></i>
                      <h4>Content Marketing Strategy</h4>
                    </div>
                  </div>
                  <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-box d-flex align-items-center">
                      <i class="bi bi-check"></i>
                      <h4>Email Marketing Campaigns</h4>
                    </div>
                  </div>
                </div>
              </div>

              <p class="mt-4">
                Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.
              </p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="service-info">
              <h4>Service Information</h4>
              <div class="info-item">
                <i class="bi bi-clock"></i>
                <h5>Project Duration</h5>
                <p>3-6 months</p>
              </div>
              <div class="info-item">
                <i class="bi bi-person-check"></i>
                <h5>Project Manager</h5>
                <p>Sarah Johnson</p>
              </div>
              <div class="info-item">
                <i class="bi bi-telephone"></i>
                <h5>Contact Support</h5>
                <p>+1 (555) 123-4567</p>
              </div>
            </div>

            <div class="related-services mt-5">
              <h4>Related Services</h4>
              <div class="service-item">
                <i class="bi bi-bar-chart"></i>
                <h5><a href="#">Business Analytics</a></h5>
                <p>Aut suscipit aut cum nemo deleniti aut omnis. Doloribus ut maiores omnis facere</p>
              </div>
              <div class="service-item">
                <i class="bi bi-briefcase"></i>
                <h5><a href="#">Business Consulting</a></h5>
                <p>Sit sint consectetur velit quisquam cupiditate impedit suscipit alias</p>
              </div>
              <div class="service-item">
                <i class="bi bi-graph-up"></i>
                <h5><a href="#">Financial Planning</a></h5>
                <p>Sed perspiciatis omnis iste natus error sit voluptatem doloremque</p>
              </div>
            </div>
          </div>

        </div>

      </div>

    </section><!-- /Service Details Section -->

@endsection
