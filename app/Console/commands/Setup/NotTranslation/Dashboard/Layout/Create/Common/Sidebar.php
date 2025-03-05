<?php

namespace App\Console\Commands\Setup\NotTranslation\Dashboard\Layout\Create\Common;

use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class Sidebar extends Command
{

    use ViewFileHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lebify:dashboard-common-sidebar';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new sidebar component';

    /**
     * Execute the console command.
     */
    public function handle()
    {
$fileContent = <<<'HTML'
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="index.html">
            <img alt="Logo" style="height: 35px"
                src="{{ asset('core/vendor/img/logo/logo-no-background-dark.svg') }}"
                class="app-sidebar-logo-default theme-light-show" />
            <img alt="Logo" style="height: 35px" src="{{ asset('core/vendor/img/logo/logo-no-background.svg') }}"
                class="app-sidebar-logo-default theme-dark-show" />
            @if (LaravelLocalization::getCurrentLocale() == 'ar')
                <img alt="Logo" style="height: 33px;transform: scaleX(-1);"
                    src="{{ asset('vendor/img/logo/logo-icon.png') }}" class="app-sidebar-logo-minimize" />
            @else
                <img alt="Logo" style="height: 33px" src="{{ asset('core/vendor/img/logo/logo-icon.png') }}"
                    class="app-sidebar-logo-minimize" />
            @endif
        </a>
        <!--end::Logo image-->
        <!--begin::Sidebar toggle-->
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="bi bi-arrow-right-short fs-3 rotate-180"></i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <!--begin::Scroll wrapper-->
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-expand="false">
                    @foreach (config('sidebar.menu_items') as $menuItem)
                        <x-lebify-sidebar :item="$menuItem" />
                    @endforeach
                </div>

                <!--end::Menu-->
            </div>
            <!--end::Scroll wrapper-->
        </div>
        <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-3" id="kt_app_sidebar_footer">
            <a href="#"
                class="btn btn-flex flex-center btn-custom btn-primary overflow-hidden text-nowrap px-0 h-40px w-100"
                data-kt-initialized="1">
                <span class="btn-label">{{ __('common.dashboard.docs_and_components') }}</span>
                <i class="bi bi-box-arrow-up-right p-0 btn-icon fs-2 m-0"></i>
            </a>
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
HTML;

        $fileName = [
            'dashboard' => 'dashboard/common',
            'file' => 'sidebar',
            'content' => $fileContent,
        ];

        if ($this->updateViewFile($fileName['file'], $fileName['content'], $fileName['dashboard'])) {
            $this->info('Sidebar file has been updated successfully!');
        } else {
            $this->error('Failed to update sidebar file!');
        }
    }
}
