<?php

namespace App\Console\Commands\Setup\NotTranslation\Auth\Update;

use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class UpdateAppLayoutCommand extends Command
{

    use ViewFileHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:app-layout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update App Layout';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appLayoutContent = <<<'VIEW'
<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" data-bs-theme="light"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{-- dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}" --}}>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>@yield('title') | {{ env('APP_NAME') }}</title>
    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('core/vendor/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('core/vendor/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('core/vendor/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('core/vendor/img/favicons/favicon.ico') }}">
    <meta name="msapplication-TileColor" content="#008382">
    <meta name="msapplication-TileImage" content="{{ asset('core/vendor/img/favicons/mstile-150x150.png') }}">
    <!-- ===============================================-->
    <!--    Package-->
    <!-- ===============================================-->
    <link rel="stylesheet" href="{{ asset('core/packages/iziToast/iziToast.min.css') }}">
    <!-- ===============================================-->
    <!--    Meta-->
    <!-- ===============================================-->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#FFFFFF" />
    <meta name="author" content="Dany Seifeddine">
    <meta name="robots" content="index, nofollow">
    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    {{-- @if (LaravelLocalization::getCurrentLocaleDirection() === 'rtl') --}}
    {{-- <link href="{{ url('vendor/css/datatables.bundle.rtl.css') }}" rel="stylesheet" type="text/css" /> --}}
    {{-- <link href="{{ url('vendor/css/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" /> --}}
    {{-- <link href="{{ url('vendor/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" /> --}}
    {{-- @else --}}
    <link href="{{ url('core/vendor/css/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('core/vendor/css/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('core/vendor/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    {{-- @endif --}}
    @stack('styles')
</head>

<body id="body" class="app-default" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px"
    data-kt-name="metronic">
    <script>
        let defaultThemeMode = "dark";
        let themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-theme-mode");
            } else {
                if (localStorage.getItem("data-theme") !== null) {
                    themeMode = localStorage.getItem("data-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        @yield('content')
    </div>
    <script src="{{ asset('core/packages/iziToast/iziToast.min.js') }}"></script>
    <script src="{{ url('core/vendor/js/plugins.bundle.js') }}"></script>
    <script src="{{ url('core/vendor/js/scripts.bundle.js') }}"></script>
    <script src="{{ url('core/vendor/js/datatables.bundle.js') }}"></script>
    <script type="module" src="{{ asset('core/global/launcher.js') }}"></script>
    @stack('scripts')
</body>

</html>
VIEW;

        if ($this->updateViewFile('auth.blade.php', $appLayoutContent, 'auth/layout')) {
            $this->info('App Layout has been updated successfully!');
        } else {
            $this->error('Failed to update app layout!');
        }

        if ($this->deleteViewPath('layouts')) {
            $this->info('View directory has been deleted successfully!');
        } else {
            $this->error('Failed to delete view directory!');
        }
    }
}
