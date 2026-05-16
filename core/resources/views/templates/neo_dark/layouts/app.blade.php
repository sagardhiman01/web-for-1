<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Core Asset">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logoIcon/logo.png') }}">
    <title> Core Asset - @yield('title', $pageTitle) </title>
    @include('partials.seo')
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/animate.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/nice-select.css') }}">

    <!-- slick slider css -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/vendor/slick.css') }}">

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">
    @stack('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">
    @stack('style')

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color=f6e05e">
</head>

<body>
    @php
        $preloader = getContent('preloader.content', true);
    @endphp
    <div class="preloader">
        <div class="preloader__imges">
            <img src="{{ getImage(getFilePath('logoIcon').'/logo.png') }}" class="preloader__img icon" alt="@lang('Logo')">
            <img src="" class="preloader__img one d-none" alt="">
        </div>
    </div>

    @stack('fbComment')

    @yield('panel')
    @include($activeTemplate . 'partials.chatbot')

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp
    @if ($cookie?->data_values?->status == 1 && !\Cookie::get('gdpr_cookie'))
        <!-- cookies dark version start -->
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie?->data_values?->short_desc }} <a href="{{ route('cookie.policy') }}" class="text--base" target="_blank">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a href="javascript:void(0)" class="btn btn--base text-white w-100 policy">@lang('Allow')</a>
            </div>
        </div>
        <!-- cookies dark version end -->
    @endif


    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

    <!-- slick slider js -->
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/vendor/wow.min.js') }}"></script>
    <!-- dashboard custom js -->
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')

    @stack('script')

    @include('partials.plugins')

    @include('partials.notify')


    <script>
        (function($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            var inputElements = $('[type=text],[type=password],[type=email],[type=number],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox') {
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }
            });


            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

        })(jQuery);
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(function(e) { console.log('SW reg failed', e); });
        }

        let deferredPrompt = null;

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            var fab = document.getElementById('pwaFab');
            if (fab) fab.style.display = 'flex';
        });

        window.addEventListener('appinstalled', function() {
            deferredPrompt = null;
            var fab = document.getElementById('pwaFab');
            if (fab) fab.style.display = 'none';
        });

        function installApp() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function(choice) {
                    if (choice.outcome === 'accepted') {
                        var fab = document.getElementById('pwaFab');
                        if (fab) fab.style.display = 'none';
                    }
                    deferredPrompt = null;
                });
            } else {
                var ua = navigator.userAgent || '';
                if (/iPhone|iPad|iPod/i.test(ua)) {
                    alert('iPhone: Safari me Share button (⬆) tap karo → "Add to Home Screen" daba do. App install ho jayega!');
                } else {
                    alert('Browser menu (⋮) kholke "Install App" ya "Add to Home Screen" par click karo. App phone me install ho jayega!');
                }
            }
        }
    </script>

    <!-- Floating Download App Button -->
    <div id="pwaFab" onclick="installApp()" style="display:none; position:fixed; bottom:24px; right:24px; z-index:9999; width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#4FD1C5,#3182CE); color:#fff; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 4px 20px rgba(79,209,197,0.5); transition:transform .2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        <i class="las la-download" style="font-size:1.6rem;"></i>
    </div>

    <style>
        #pwaFab { animation: pwaFloat 3s ease-in-out infinite; }
        @keyframes pwaFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        #pwaFab:hover { animation: none; }
    </style>

</body>

</html>
