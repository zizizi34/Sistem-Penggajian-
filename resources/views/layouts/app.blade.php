<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>@yield('title') - {{ config('app.name') }}</title>

  <link rel="stylesheet" href="{{ asset('css/main/app.css') }}" />
  <link rel="shortcut icon" href="{{ asset('images/logo/laguna.png') }}" type="image/png" />
  <link rel="shortcut icon" href="{{ asset('images/logo/laguna.png') }}" type="image/png" />

  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <link rel="stylesheet" href="{{ asset('css/shared/iconly.css') }}" />
  @vite([])

  <style>
    /* Auto-dismiss alert animation */
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .alert.auto-dismiss {
      animation: slideDown 0.3s ease-out;
      position: relative;
      overflow: hidden;
    }

    .alert.auto-dismiss::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: currentColor;
      opacity: 0.3;
      animation: shrink 5s linear forwards;
    }

    @keyframes shrink {
      from {
        width: 100%;
      }
      to {
        width: 0;
      }
    }
    
    /* Custom Sidebar Menu Padding & Centering */
    .sidebar-wrapper .menu {
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }

    /* Sidebar Background Color Customization */
    .sidebar-wrapper {
      background-color: #1C352D !important;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-wrapper .sidebar-header {
      background-color: #1C352D !important;
    }

    .sidebar-wrapper .sidebar-title {
      color: rgba(255, 255, 255, 0.8) !important;
      font-weight: 700 !important;
      letter-spacing: 0.5px;
    }

    .sidebar-wrapper .sidebar-link {
      color: rgba(255, 255, 255, 0.9) !important;
      border-radius: 0.5rem !important;
      margin: 2px 0;
      transition: all 0.3s ease;
    }

    .sidebar-wrapper .sidebar-link i, 
    .sidebar-wrapper .sidebar-link span {
      color: rgba(255, 255, 255, 0.9) !important;
    }

    .sidebar-wrapper .sidebar-link:hover {
      background-color: rgba(255, 255, 255, 0.15) !important;
    }

    .sidebar-wrapper .sidebar-item.active > .sidebar-link {
      background-color: #ffffff !important;
    }

    .sidebar-wrapper .sidebar-item.active > .sidebar-link i,
    .sidebar-wrapper .sidebar-item.active > .sidebar-link span {
      color: #1C352D !important;
    }

    .sidebar-toggler.x i {
      color: #ffffff !important;
    }

    /* Dashboard Background Color */
    body {
      background-color: #F8F8F8 !important;
    }

    #main {
      background-color: #F8F8F8 !important;
      min-height: 100vh;
    }

    /* ─── MODERN MODAL STYLES ─────────────────────────── */
    .modal-content {
      border: none;
      border-radius: 1.25rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      background-color: #ffffff;
    }

    .modal-header {
      border-bottom: 1px solid #edf2f7;
      padding: 1.5rem 2rem;
      background-color: #ffffff;
      display: flex;
      align-items: center;
    }

    .modal-header .modal-title {
      font-weight: 700;
      color: #1C352D;
      font-size: 1.25rem;
      letter-spacing: -0.025em;
    }

    .modal-header .btn-close {
      background-color: #f7fafc;
      padding: 0.75rem;
      border-radius: 50%;
      transition: all 0.2s;
      opacity: 0.8;
    }

    .modal-header .btn-close:hover {
      background-color: #edf2f7;
      transform: rotate(90deg);
      opacity: 1;
    }

    .modal-body {
      padding: 2rem;
    }

    .modal-footer {
      border-top: 1px solid #edf2f7;
      padding: 1.25rem 2rem;
      background-color: #f8fafc;
    }

    /* Form Modernization */
    .form-label {
      font-weight: 600;
      color: #4a5568;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    .form-control, .form-select {
      border: 1.5px solid #e2e8f0;
      border-radius: 0.75rem;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      transition: all 0.2s ease;
      background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
      border-color: #1C352D;
      background-color: #ffffff;
      box-shadow: 0 0 0 4px rgba(28, 53, 45, 0.1);
      outline: none;
    }

    .input-group-text {
      background-color: #f1f5f9;
      border: 1.5px solid #e2e8f0;
      border-right: none;
      border-radius: 0.75rem 0 0 0.75rem;
      color: #64748b;
      font-weight: 600;
    }

    .input-group > .form-control {
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
    }

    /* Modern Buttons inside Modal */
    .modal-footer .btn {
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      border-radius: 0.75rem;
      transition: all 0.2s;
    }

    .btn-submit-modal {
      background-color: #1C352D;
      color: white;
      border: none;
    }

    .btn-submit-modal:hover {
      background-color: #142a24;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(28, 53, 45, 0.2);
    }

    .btn-cancel-modal {
      background-color: #ffffff;
      color: #718096;
      border: 1px solid #e2e8f0;
    }

    .btn-cancel-modal:hover {
      background-color: #f7fafc;
      color: #2d3748;
    }

    /* Custom Animation for Modals */
    .modal.fade .modal-dialog {
      transform: scale(0.9) translateY(20px);
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .modal.show .modal-dialog {
      transform: scale(1) translateY(0);
    }
  </style>
</head>

<body>
  <div id="app">
    <div id="sidebar" class="active">
      <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
          <div class="d-flex justify-content-center align-items-center position-relative w-100">
            <div class="logo">
              <a href=""><img src="{{ asset('images/logo/laguna.png') }}" alt="Laguna Group Logo" style="height: 120px;"></a>
            </div>
            <div class="sidebar-toggler x position-absolute" style="right: 0;">
              <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
          </div>
        </div>
        @auth('administrator')
        @include('layouts.administrator.sidebar')
        @endauth

        @auth('officer')
        @include('layouts.officer.sidebar')
        @endauth

        @auth('student')
        @include('layouts.student.sidebar')
        @endauth
      </div>
    </div>
    <div id="main">
      <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
          <i class="bi bi-justify fs-3"></i>
        </a>
      </header>

      <div class="page-heading">
        <div class="page-title">
          <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
              <h3>@yield('title', 'Default title').</h3>
              <p class="text-subtitle text-muted">@yield('description', 'Default description').</p>
            </div>
          </div>
        </div>
        @yield('content')
      </div>

      <footer>
        <div class="footer clearfix mb-0 text-muted">
          <div class="float-start">
            <p>2025 &copy; Laguna Group</p>
          </div>
          <div class="float-end">
            <p>
              <a href="">Narendra</a>
            </p>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <script src="{{ asset('js/bootstrap.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  @stack('modal')
  @stack('script')

  <script>
    $(function () {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

      $('.datatable').DataTable({
        pageLength: 5,
        lengthMenu: [[5, 10, 15, 20, 25, 50, -1], [5, 10, 15, 20, 25, 50, "All"]],
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.3/i18n/id.json',
        },
      });

      $('input[type=date]').flatpickr({
        allowInput: true,
      });

      $('.btn-delete').click(function (e) {
        e.preventDefault();
        Swal.fire({
          title: 'Yakin?',
          text: "Data tersebut akan dihapus",
          icon: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya!',
          cancelButtonText: 'Tidak',
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).parent().submit();
          }
        });
      });

      $('.btn-returned').click(function (e) {
        e.preventDefault();
        Swal.fire({
          title: 'Kembalikan?',
          text: "Status peminjaman tersebut akan berubah menjadi sudah kembali",
          icon: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya!',
          cancelButtonText: 'Tidak',
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).parent().parent().submit();
          }
        });
      });

      $('.btn-validate').click(function (e) {
        e.preventDefault();
        Swal.fire({
          title: 'Validasi?',
          text: "Status validasi peminjaman tersebut akan terisi Anda",
          icon: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya!',
          cancelButtonText: 'Tidak',
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).parent().submit();
          }
        });
      });

      $('#logout').click(function (e) {
        e.preventDefault();
        Swal.fire({
          title: 'Keluar?',
          text: "Anda akan keluar dari aplikasi",
          icon: 'warning',
          showCancelButton: true,
          reverseButtons: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya!',
          cancelButtonText: 'Tidak',
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).parent().submit();
          }
        });
      });
    });

    // ─── CEGAH TOMBOL BACK SETELAH LOGOUT ───────────────────────────
    // pageshow terpicu saat halaman dari bfcache (browser back/forward cache)
    // persisted = true artinya halaman diambil dari cache, bukan dari server
    window.addEventListener('pageshow', function(event) {
      if (event.persisted) {
        // Halaman diambil dari bfcache — paksa reload agar server cek session
        window.location.reload();
      }
    });

    // Tandai halaman ini sebagai halaman authenticated
    // Jika session sudah tidak valid, server akan redirect ke login
    window.history.pushState(null, null, window.location.href);
    window.addEventListener('popstate', function() {
      window.history.pushState(null, null, window.location.href);
    });
    // ────────────────────────────────────────────────────────────────
  </script>

</body>

</html>
