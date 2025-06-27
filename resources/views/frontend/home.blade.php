<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Cafe Bahagia Banget - Home</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Amatic+SC:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset("frontend/assets/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/assets/vendor/aos/aos.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/assets/vendor/glightbox/css/glightbox.min.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/assets/vendor/swiper/swiper-bundle.min.css") }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset("frontend/assets/css/main.css") }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Yummy
  * Updated: Jul 27 2023 with Bootstrap v5.3.1
  * Template URL: https://bootstrapmade.com/yummy-bootstrap-restaurant-website-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  @include('frontend.layouts.topbar')
  <!-- End Header -->

  <!-- ======= Menu Section ======= -->
  <section id="menu" class="menu">
    <div class="container" data-aos="fade-up">
      <div class="section-header">
        <p>Check Our <span>Menu</span></p>
      </div>

      <div class="tab-content" data-aos="fade-up" data-aos-delay="300">
        

          <div class="row gy-5">
                @foreach ($products as $item)
                <div class="col-lg-4 menu-item">
                    <a href="assets/img/menu/menu-item-1.png" class="glightbox"><img src="assets/img/menu/menu-item-1.png" class="menu-img img-fluid" alt=""></a>
                    <h4>{{ $item->nama_produk }}</h4>
                    <p>Kategori : {{ $item->nama_kategori }}</p>
                    <p class="price">
                        Rp {{ $item->harga_satuan }}
                    </p>
                    <form class="myForm" action="{{ route('quote') }}" method="get">
                        @csrf()
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <button type="submit" id="submitForm" class="btn btn-primary btn-user btn-block">
                            Beli Produk
                        </button>
                    </form>
                </div><!-- Menu Item -->
                @endforeach
            </div><!-- End Starter Menu Content -->
      </div>

    </div>
  </section><!-- End Menu Section -->

  <!-- ======= Footer ======= -->
  @include('frontend.layouts.footer')
  <!-- End Footer -->

  <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset("frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
  <script src="{{ asset("frontend/assets/vendor/aos/aos.js") }}"></script>
  <script src="{{ asset("frontend/assets/vendor/glightbox/js/glightbox.min.js") }}"></script>
  <script src="{{ asset("frontend/assets/vendor/purecounter/purecounter_vanilla.js") }}"></script>
  <script src="{{ asset("frontend/assets/vendor/swiper/swiper-bundle.min.js") }}"></script>
  <script src="{{ asset("frontend/assets/vendor/php-email-form/validate.js") }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset("frontend/assets/js/main.js") }}"></script>

  {{-- Toastr JS --}}
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    @if(Session::has('message'))
    var type = "{{ Session::get('alert-type','info') }}"
    switch(type){
       case 'info':
       toastr.info(" {{ Session::get('message') }} ");
       break;
   
       case 'success':
       toastr.success(" {{ Session::get('message') }} ");
       break;
   
       case 'warning':
       toastr.warning(" {{ Session::get('message') }} ");
       break;
   
       case 'error':
       toastr.error(" {{ Session::get('message') }} ");
       break; 
    }
    @endif 
   </script>

</body>

</html>





























