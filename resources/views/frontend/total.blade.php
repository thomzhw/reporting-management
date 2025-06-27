<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}" />

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
  <section id="menu" class="menu">
    <div class="container" data-aos="fade-up">
      <div class="section-header">
        <p>Order <span>Menu</span></p>
      </div>
        @if ($product_choose)
        @if ($noPesan)
        @if ($nama)
        @if ($jumlah)
        @if ($tanggal)
        <div class="mb-3">
            <form action="{{ route('totalStore') }}" method="post">
                @csrf()
                <div class="form-group">
                    <label for="exampleFormControlTextarea1" class="form-label">Nama Pelanggan</label>
                    <input type="hidden" class="form-control" id="exampleFormControlInput1" name="nama" value="{{ $nama }}">
                    <input type="text" class="form-control" id="exampleFormControlInput1" name="nama" value="{{ $nama }}" disabled>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1" class="form-label">Nama Produk</label>
                    <input type="hidden" class="form-control" id="exampleFormControlInput1" name="id" value="{{ $product_choose->id }}">
                    <input type="text" class="form-control" id="exampleFormControlInput1" name="id" value="{{ $product_choose->nama_produk }}" disabled>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1" class="form-label">No Pemesanan</label>
                    <input type="hidden" class="form-control" id="exampleFormControlInput1" name="noPesan" value="{{ $noPesan }}">
                    <input type="text" class="form-control" id="exampleFormControlInput1" maxlength="2" name="noPesan" value="{{ $noPesan }}" disabled>
                </div>
                <div class="col-lg-1">
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1" class="form-label">Jumlah</label>
                        <input type="hidden" class="form-control" id="exampleFormControlInput1" name="jumlah" value="{{ $jumlah }}">
                        <input type="number" class="form-control" id="exampleFormControlInput1" name="jumlah" value="{{ $jumlah }}" disabled>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1" class="form-label">Tanggal Pemesanan</label>
                        <input type="hidden" class="form-control" id="exampleFormControlInput1" name="tglPemesanan" value="{{ $tanggal }}">
                        <input type="date" class="form-control" id="exampleFormControlInput1" value="{{ $tanggal }}"  name="tglPemesanan" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1" class="form-label">Total Bayar</label>
                    <input type="hidden" class="form-control" id="exampleFormControlInput1" name="total" value="{{ $total }}">
                    <input type="text" class="form-control" id="exampleFormControlInput1"  name="total" value="Rp.{{ $total }}" disabled>
                </div>
                {{-- <div class="form-group"> --}}
                    {{-- <label for="exampleFormControlTextarea1" class="form-label">Harga Total</label> --}}
                    {{-- <input type="hidden" class="form-control" id="exampleFormControlInput1" name="total"> --}}
                {{-- </div> --}}
                <button type="submit" id="submitForm" class="btn btn-primary btn-user btn-block mt-3">
                    Pesan
                </button>
            </form>
        </div>
        @endif
            
        @endif
            
        @endif
            
        @endif
            
        @endif

    </section>

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

</body>

</html>





























