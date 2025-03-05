<?php

namespace App\Console\Commands\Setup\Translation\Auth\Update;

use App\Traits\Commands\ViewFileHandler;
use Illuminate\Console\Command;

class UpdateAuthFilesTCommand extends Command
{

    use ViewFileHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:auth-files-t';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Auth Files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $authPath = 'auth';
        $passwordPath = 'auth/passwords';
        $footerPath = 'auth/components';

        $fileName = [
            'login' => 'login.blade.php',
            'register' => 'register.blade.php',
            'verify' => 'verify.blade.php',
            'email' => 'email.blade.php',
            'reset' => 'reset.blade.php',
            'footer' => 'footer.blade.php'
        ];

        $loginContent = <<<VIEW
@extends('auth.layout.auth')

@section('title', 'Login')


@section('content')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="auth d-flex flex-column flex-center flex-column-fluid p-10">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">

                <!--begin::Aside-->
                <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column-fluid flex-column w-100 mw-450px">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack py-2">
                            <!--begin::Back link-->
                            <div class="me-2"></div>
                            <!--end::Back link-->
                            <!--begin::Sign Up link-->
                            <div class="m-0">
                                <span class="text-gray-500 fw-bold fs-5 me-2">{{ __('auth.not_a_member') }}</span>
                                <a href="{{ route('register') }}"
                                    class="text-logo fw-bold fs-5">{{ __('auth.sign_up') }}</a>
                            </div>
                            <!--end::Sign Up link=-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="text-center fs-2 fw-bold py-20">
                            <img src="{{ asset('core/vendor/img/logo/logo-icon.png') }}" width="70" alt=""
                                style="animation: spin 2s linear infinite;">
                        </div>
                        <!--begin::Form-->
                        <form form-id="login" route="{{ route('login') }}" identifier="single-form-post-handler"
                            http-request feedback redirect>

                            <!--begin::Body-->
                            <div class="card-body">
                                <!--begin::Heading-->
                                <div class="text-start mb-10">
                                    <!--begin::Title-->
                                    <h1 class="text-gray-900 mb-3 fs-3x">{{ __('auth.join_now') }}</h1>
                                    <!--end::Title-->
                                    <!--begin::Text-->
                                    <div class="text-gray-500 fw-semibold fs-6">{{ __('auth.explore_a_sanctuary') }}
                                    </div>
                                    <!--end::Link-->
                                </div>
                                <!--begin::Heading-->
                                <!--begin::Input group=-->
                                <div class="fv-row mb-8">
                                    <!--begin::Email-->
                                    <input type="text" feedback-id="email-feedback"
                                        placeholder="{{ __('common.email') }}" id="email" name="email"
                                        autocomplete="off" class="form-control form-control-solid form-control form-control-solid-solid" />
                                    <div id="email-feedback" class="invalid-feedback fw-bold"></div>
                                    <!--end::Email-->
                                </div>
                                <!--end::Input group=-->
                                <div class="fv-row mb-7">
                                    <!--begin::Password-->
                                    <input type="password" feedback-id="password-feedback"
                                        placeholder="{{ __('common.password') }}" id="password" name="password"
                                        autocomplete="off" class="form-control form-control-solid form-control form-control-solid-solid" />
                                    <div id="password-feedback" class="invalid-feedback fw-bold"></div>
                                    <!--end::Password-->
                                </div>
                                <!--end::Input group=-->
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-10">
                                    <div></div>
                                    <!--begin::Link-->
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                            class="text-logo">{{ __('auth.forget_password') }}</a>
                                    @endif
                                    <!--end::Link-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Actions-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Submit-->
                                    <button submit-form-id="login" loading-text="{{ __('common.loggingin') }}"
                                        class="btn bg-logo d-flex align-items-center justify-content-center gap-2"
                                        type="submit">
                                        {{ __('validation.attributes.login') }}
                                    </button>
                                    <!--end::Submit-->
                                    <!--begin::Social-->
                                    <div class="d-flex align-items-center">
                                        <div class="text-gray-500 fw-semibold fs-6 me-3 me-md-6">{{ __('auth.or') }}
                                        </div>
                                        <!--begin::Symbol-->
                                        <a href="#" class="symbol symbol-circle symbol-45px w-45px bg-light me-3">
                                            <img alt="Logo" src="{{ asset('core/vendor/img/icon/google-icon.svg') }}"
                                                class="p-4" />
                                        </a>
                                        <!--end::Symbol-->
                                        <!--begin::Symbol-->
                                        <a href="#" class="symbol symbol-circle symbol-45px w-45px bg-light me-3">
                                            <img alt="Logo" src="{{ asset('core/vendor/img/icon/facebook-3.svg') }}"
                                                class="p-4" />
                                        </a>
                                        <!--end::Symbol-->
                                    </div>
                                    <!--end::Social-->
                                </div>
                                <!--end::Actions-->
                            </div>

                            <!--begin::Body-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    {{-- multi lang --}}
                    @include('auth.components.footer')
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--begin::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
@endsection
VIEW;

        $registerContent = <<<VIEW
@extends('auth.layout.auth')

@section('title', 'Register')


@section('content')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="auth d-flex flex-column flex-center flex-column-fluid p-10">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Aside-->
                <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column-fluid flex-column w-100 mw-450px">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack py-2">
                            <!--begin::Back link-->
                            <div class="me-2"></div>
                            <!--end::Back link-->
                            <!--begin::Sign Up link-->
                            <div class="m-0">
                                <span class="text-gray-500 fw-bold fs-5 me-2">{{ __('auth.already_have_account') }}</span>
                                <a href="{{ route('login') }}"
                                    class="text-logo fw-bold fs-5">{{ __('actions.sign_in') }}</a>
                            </div>
                            <!--end::Sign Up link=-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="text-center fs-2 fw-bold py-20">
                            <img src="{{ asset('core/vendor/img/logo/logo-icon.png') }}" width="70" alt=""
                                style="animation: spin 2s linear infinite;">
                        </div>
                        <!--begin::Form-->
                        <form form-id="register" route="{{ route('register') }}" identifier="single-form-post-handler"
                            http-request feedback redirect>
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-start mb-10">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 mb-3 fs-3x">{{ __('auth.join_now') }}</h1>
                                <!--end::Title-->
                                <!--begin::Text-->
                                <div class="text-gray-500 fw-semibold fs-6">{{ __('auth.explore_a_sanctuary') }}</div>
                                <!--end::Link-->
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="row fv-row mb-7">
                                <!--begin::Col-->
                                <div class="col-xl-12">
                                    <input class="form-control form-control-solid form-control form-control-solid-lg form-control form-control-solid-solid" type="text"
                                        placeholder="{{ __('common.full_name') }}" feedback-id="name-feedback"
                                        name="name" id="name" autocomplete="off" />
                                    <div id="name-feedback" class="invalid-feedback fw-bold"></div>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-7">
                                <input class="form-control form-control-solid form-control form-control-solid-lg form-control form-control-solid-solid" type="text"
                                    placeholder="{{ __('common.email') }}" feedback-id="email-feedback" name="email"
                                    id="email" autocomplete="off" />
                                <div id="email-feedback" class="invalid-feedback fw-bold"></div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-10" data-kt-password-meter="true">
                                <!--begin::Wrapper-->
                                <div class="mb-1">
                                    <!--begin::Input wrapper-->
                                    <div class="position-relative mb-3">
                                        <input class="form-control form-control-solid form-control form-control-solid-lg form-control form-control-solid-solid" type="password"
                                            placeholder="{{ __('common.password') }}" feedback-id="password-feedback"
                                            name="password" autocomplete="off" id="password" />
                                        <div id="password-feedback" class="invalid-feedback fw-bold"></div>
                                        <span
                                            class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                            data-kt-password-meter-control="visibility">
                                            <i class="ki-duotone ki-eye-slash fs-2"></i>
                                            <i class="ki-duotone ki-eye fs-2 d-none"></i>
                                        </span>
                                    </div>
                                    <!--end::Input wrapper-->
                                    <!--begin::Meter-->
                                    <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                    </div>
                                    <!--end::Meter-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Hint-->
                                <div class="text-muted" data-kt-translate="sign-up-hint">{{ __('auth.use_8_character') }}
                                </div>
                                <!--end::Hint-->
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-10">
                                <input class="form-control form-control-solid form-control form-control-solid-lg form-control form-control-solid-solid" type="password"
                                    placeholder="{{ __('common.confirm_password') }}"
                                    feedback-id="password_confirmation-feedback" id="password_confirmation"
                                    name="password_confirmation" autocomplete="off" />
                                <div id="password_confirmation-feedback" class="invalid-feedback fw-bold"></div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="d-flex flex-stack">
                                <!--begin::Submit-->
                                <button class="btn bg-logo d-flex align-items-center justify-content-center gap-2"
                                    loading-text="{{ __('common.registering') }}" submit-form-id="register" type="submit">
                                    {{ __('auth.register') }}
                                </button>
                                <!--end::Submit-->
                                <!--begin::Social-->
                                <div class="d-flex align-items-center">
                                    <div class="text-gray-500 fw-semibold fs-6 me-3 me-md-6">{{ __('auth.or') }}</div>
                                    <!--begin::Symbol-->
                                    <a href="#" class="symbol symbol-circle symbol-45px w-45px bg-light me-3">
                                        <img alt="Logo" src="{{ asset('core/vendor/img/icon/google-icon.svg') }}"
                                            class="p-4" />
                                    </a>
                                    <!--end::Symbol-->
                                    <!--begin::Symbol-->
                                    <a href="#" class="symbol symbol-circle symbol-45px w-45px bg-light me-3">
                                        <img alt="Logo" src="{{ asset('core/vendor/img/icon/facebook-3.svg') }}"
                                            class="p-4" />
                                    </a>
                                    <!--end::Symbol-->
                                </div>
                                <!--end::Social-->
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    {{-- multi lang --}}
                    @include('auth.components.footer')
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>

            <!--begin::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
@endsection
VIEW;

        $verifyContent = <<<VIEW
@extends('auth.layout.auth')

@section('title', 'Verification')


@section('content')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="auth d-flex flex-column flex-center flex-column-fluid p-10">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Aside-->
                <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column-fluid flex-column w-100 mw-450px">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack py-2 justify-content-end">
                            <!--begin::Sign Up link-->
                            <div class="m-0 d-flex">
                                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                    @csrf
                                    <a href=""
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="text-logo fw-bold fs-5">{{ __('actions.log_out') }}</a>
                                </form>
                            </div>
                            <!--end::Sign Up link=-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="text-center fs-2 fw-bold py-20">
                            <img src="{{ asset('core/vendor/img/logo/logo-icon.png') }}" width="70" alt=""
                                style="animation: spin 2s linear infinite;">
                        </div>
                        <!--begin::Form-->
                        <form form-id="verify" feedback route="{{ route('verification.resend') }}"
                            identifier="single-form-post-handler" class="py-20" http-request success-toast>
                            @csrf

                            <!--begin::Heading-->
                            <div class="text-start mb-10">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 mb-3 fs-3x" data-kt-translate="new-password-title">
                                    {{ __('auth.email_verification') }}</h1>
                                <!--end::Title-->
                                <!--begin::Text-->
                                <div class="text-gray-500 fw-semibold fs-6" data-kt-translate="new-password-desc">
                                    {{ __('auth.access_features') }}
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Heading-->

                            <!--begin::Actions-->
                            <div class="d-flex flex-stack">
                                <!--begin::Link-->
                                <button class="btn bg-logo d-flex align-items-center justify-content-center gap-2"
                                    loading-text="{{ __('common.sending') }}" submit-form-id="verify" type="submit">
                                    {{ __('auth.send_verification_email') }}
                                </button>
                                <!--end::Link-->
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    {{-- multi lang --}}
                    @include('auth.components.footer')
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>

            <!--begin::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
@endsection
VIEW;

        $emailContent = <<<VIEW
@extends('auth.layout.auth')

@section('title', 'email')


@section('content')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="auth d-flex flex-column flex-center flex-column-fluid p-10">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Aside-->
                <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column-fluid flex-column w-100 mw-450px">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack py-2">
                            <!--begin::Back link-->
                            <div class="me-2"></div>
                            <!--end::Back link-->
                            <!--begin::Sign Up link-->
                            <div class="m-0">
                                <span class="text-gray-500 fw-bold fs-5 me-2">{{ __('auth.not_a_member') }}</span>
                                <a href="{{ route('register') }}"
                                    class="text-logo fw-bold fs-5">{{ __('auth.sign_up') }}</a>
                            </div>
                            <!--end::Sign Up link=-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="text-center fs-2 fw-bold py-20">
                            <img src="{{ asset('core/vendor/img/logo/logo-icon.png') }}" width="70" alt=""
                                style="animation: spin 2s linear infinite;">
                        </div>
                        <!--begin::Form-->
                        <form form-id="send-email" feedback route="{{ route('password.email') }}"
                            identifier="single-form-post-handler" class="py-10" http-request success-toast>
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-start mb-10">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 mb-3 fs-3x">{{ __('auth.forget_password') }}</h1>
                                <!--end::Title-->
                                <!--begin::Text-->
                                <div class="text-gray-500 fw-semibold fs-6">
                                    {{ __('auth.enter_reset_email') }}
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--begin::Heading-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-10 fv-plugins-icon-container">
                                <input type="text" feedback-id="email-feedback" placeholder="{{ __('common.email') }}"
                                    id="email" name="email" autocomplete="off"
                                    class="form-control form-control-solid form-control form-control-solid-solid" />
                                <div id="email-feedback" class="invalid-feedback fw-bold"></div>
                            </div>

                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="d-flex flex-stack">
                                <!--begin::Link-->
                                <div class="m-0 d-flex align-items-center gap-2">
                                    <button class="btn bg-logo d-flex align-items-center justify-content-center gap-2"
                                        loading-text="{{ __('common.sending') }}" submit-form-id="send-email"
                                        type="submit">
                                        {{ __('auth.send_pass') }}
                                    </button>
                                    <a href="{{ route('login') }}"
                                        class="btn text-logo fw-bold btn-hover">{{ __('common.cancel') }}</a>
                                </div>
                                <!--end::Link-->
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    {{-- multi lang --}}
                    @include('auth.components.footer')
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>

            <!--begin::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>
@endsection
VIEW;

        $resetContent = <<<VIEW
@@extends('auth.layout.auth')

@section('title', 'Reset')


@section('content')
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="auth d-flex flex-column flex-center flex-column-fluid p-10">
            <!--begin::Authentication - Sign-in -->
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <!--begin::Aside-->
                <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column-fluid flex-column w-100 mw-450px">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack py-2">
                            <!--begin::Back link-->
                            <div class="me-2">
                                <a href="{{ route('register') }}" class="btn btn-icon bg-light rounded-circle">
                                    <i class="ki-duotone ki-black-left fs-2 text-gray-800"></i>
                                </a>
                            </div>
                            <!--end::Back link-->
                            <!--begin::Sign Up link-->
                            <div class="m-0">
                                <span class="text-gray-500 fw-bold fs-5 me-2">{{ __('auth.already_have_account') }}</span>
                                <a href="{{ route('login') }}"
                                    class="text-logo fw-bold fs-5">{{ __('actions.sign_in') }}</a>
                            </div>
                            <!--end::Sign Up link=-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="text-center fs-2 fw-bold py-20">
                            <img src="{{ asset('core/vendor/img/logo/logo-icon.png') }}" width="70" alt=""
                                style="animation: spin 2s linear infinite;">
                        </div>
                        <!--begin::Form-->
                        <form form-id="reset" feedback route="{{ route('password.update') }}"
                            identifier="single-form-post-handler"
                            class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" redirect log="true"
                            http-request>
                            @csrf

                            <input type="hidden" name="token" value="{{ \$token }}">
                            <!--begin::Heading-->
                            <div class="text-start mb-10">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 mb-3 fs-3x" data-kt-translate="new-password-title">
                                    {{ __('auth.setup_new_password') }}</h1>
                                <!--end::Title-->
                                <!--begin::Text-->
                                <div class="text-gray-500 fw-semibold fs-6" data-kt-translate="new-password-desc">
                                    {{ __('auth.has_reset_password') }}</div>
                                <!--end::Link-->
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input type="text" placeholder="{{ __('common.email') }}" id="email" name="email"
                                    autocomplete="off" feedback-id="email-feedback" class="form-control form-control-solid form-control form-control-solid-solid"
                                    value="{{ \$email ?? old('email') }}" />
                                <div id="email-feedback" class="invalid-feedback fw-bold"></div>
                                <!--end::Email-->
                            </div>
                            <div class="mb-10 fv-row fv-plugins-icon-container" data-kt-password-meter="true">
                                <!--begin::Wrapper-->
                                <div class="mb-1">
                                    <!--begin::Input wrapper-->
                                    <div class="position-relative mb-3">
                                        <input class="form-control form-control-solid form-control form-control-solid-lg form-control form-control-solid-solid" type="password"
                                            placeholder="{{ __('common.password') }}" name="password" autocomplete="off"
                                            feedback-id="password-feedback" id="password">
                                        <span
                                            class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                            data-kt-password-meter-control="visibility">
                                            <i class="ki-duotone ki-eye-slash fs-2"></i>
                                            <i class="ki-duotone ki-eye fs-2 d-none"></i>
                                        </span>
                                    </div>
                                    <!--end::Input wrapper-->
                                    <!--begin::Meter-->
                                    <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                    </div>
                                    <div id="password-feedback" class="invalid-feedback fw-bold"></div>
                                    <!--end::Meter-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Hint-->
                                <div class="text-muted" data-kt-translate="new-password-hint">
                                    {{ __('common.use_8_character') }}</div>
                                <!--end::Hint-->
                                <div
                                    class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                </div>
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Input group=-->
                            <div class="fv-row mb-10 fv-plugins-icon-container">
                                <input class="form-control form-control-solid form-control form-control-solid-lg form-control form-control-solid-solid" type="password"
                                    placeholder="{{ __('common.confirm_password') }}" name="password_confirmation"
                                    autocomplete="off" feedback-id="password_confirmation-feedback"
                                    id="password_confirmation">
                                <div id="password_confirmation-feedback" class="invalid-feedback fw-bold"></div>
                            </div>
                            <!--end::Input group=-->
                            <!--begin::Actions-->
                            <div class="d-flex flex-stack">
                                <!--begin::Link-->
                                <button class="btn bg-logo d-flex align-items-center justify-content-center gap-2"
                                    loading-text="{{ __('common.reseting') }}" submit-form-id="reset" type="submit">
                                    {{ __('auth.reset_password') }}
                                </button>
                                <!--end::Link-->
                                <!--begin::Social-->
                                <div class="d-flex align-items-center">
                                    <div class="text-gray-500 fw-semibold fs-6 me-3 me-md-6">{{ __('common.or') }}</div>
                                    <!--begin::Symbol-->
                                    <a href="#" class="symbol symbol-circle symbol-45px w-45px bg-light me-3">
                                        <img alt="Logo" src="{{ asset('core/vendor/img/icon/google-icon.svg') }}"
                                            class="p-4" />
                                    </a>
                                    <!--end::Symbol-->
                                    <!--begin::Symbol-->
                                    <a href="#" class="symbol symbol-circle symbol-45px w-45px bg-light me-3">
                                        <img alt="Logo" src="{{ asset('core/vendor/img/icon/facebook-3.svg') }}"
                                            class="p-4" />
                                    </a>
                                    <!--end::Symbol-->
                                </div>
                                <!--end::Social-->
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    {{-- multi lang --}}
                    @include('auth.components.footer')
                    <!--end::Footer-->
                </div>
                <!--end::Wrapper-->
            </div>

            <!--begin::Body-->
        </div>
        <!--end::Authentication - Sign-in-->
    </div>

@endsection
VIEW;



        $footerContent = <<<VIEW
        <div class="d-flex align-items-center justify-content-around gap-20">
    <div class="m-0 mt-5 px-5">
        <!--begin::Toggle-->
        @switch(App::getLocale())
            @case('en')
                <button class="btn btn-flex btn-link rotate" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start"
                    data-kt-menu-offset="0px, 0px">
                    <img data-kt-element="current-lang-flag" class="w-25px h-25px rounded-circle me-3"
                        src="{{ asset('core/vendor/img/flags/united-states.svg') }}" alt="" />
                    <span data-kt-element="current-lang-name" class="me-2">{{ __('common.english') }}</span>
                    <i class="ki-duotone ki-down fs-2 text-muted rotate-180 m-0"></i>
                </button>
            @break

            @case('ar')
                <button class="btn btn-flex btn-link rotate" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start"
                    data-kt-menu-offset="0px, 0px">
                    <img data-kt-element="current-lang-flag" class="w-25px h-25px rounded-circle me-3"
                        src="{{ asset('core/vendor/img/flags/saudi-arabia.svg') }}" alt="" />
                    <span data-kt-element="current-lang-name" class="me-2">{{ __('common.arabic') }}</span>
                    <i class="ki-duotone ki-down fs-2 text-muted rotate-180 m-0"></i>
                </button>
            @break

            @case('fr')
                <button class="btn btn-flex btn-link rotate" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start"
                    data-kt-menu-offset="0px, 0px">
                    <img data-kt-element="current-lang-flag" class="w-25px h-25px rounded-circle me-3"
                        src="{{ asset('core/vendor/img/flags/france.svg') }}" alt="" />
                    <span data-kt-element="current-lang-name" class="me-2">{{ __('common.french') }}</span>
                    <i class="ki-duotone ki-down fs-2 text-muted rotate-180 m-0"></i>
                </button>
            @break

            @default
        @endswitch
        <!--end::Toggle-->
        <!--begin::Menu-->
        <div class="languageBox menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-4"
            data-kt-menu="true" id="kt_auth_lang_menu">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="{{ LaravelLocalization::getLocalizedURL('en') }}" class="menu-link d-flex px-5"
                    data-kt-lang="English">
                    <span class="symbol symbol-20px me-4">
                        <img data-kt-element="lang-flag" class="rounded-1"
                            src="{{ asset('core/vendor/img/flags/united-states.svg') }}" alt="" />
                    </span>
                    <span data-kt-element="lang-name">{{ __('common.english') }}</span>
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="{{ LaravelLocalization::getLocalizedURL('ar') }}" class="menu-link d-flex px-5"
                    data-kt-lang="Saudi-arabia">
                    <span class="symbol symbol-20px me-4">
                        <img data-kt-element="lang-flag" class="rounded-1"
                            src="{{ asset('core/vendor/img/flags/saudi-arabia.svg') }}" alt="" />
                    </span>
                    <span data-kt-element="lang-name">{{ __('common.arabic') }}</span>
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <a href="{{ LaravelLocalization::getLocalizedURL('fr') }}" class="menu-link d-flex px-5"
                    data-kt-lang="French">
                    <span class="symbol symbol-20px me-4">
                        <img data-kt-element="lang-flag" class="rounded-1"
                            src="{{ asset('core/vendor/img/flags/france.svg') }}" alt="" />
                    </span>
                    <span data-kt-element="lang-name">{{ __('common.french') }}</span>
                </a>
            </div>
            <!--end::Menu item-->
        </div>
        <!--end::Menu-->
    </div>

    {{-- <div class="app-navbar-item ms-1 ms-md-4 mt-5">
        <!--begin::Menu toggle-->
        <a href="#"
            class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px show menu-dropdown"
            data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end">
            <i class="bi bi-brightness-high theme-light-show fs-1"></i>
            <i class="bi bi-moon theme-dark-show fs-1"></i>
        </a>
        <!--begin::Menu toggle-->
        <!--begin::Menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-60px show"
            data-kt-menu="true" data-kt-element="theme-mode-menu"
            style="z-index: 107; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(-78px, 74px, 0px);"
            data-popper-placement="bottom-end">
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2 active" data-kt-element="mode" data-kt-value="light">
                    <i class="bi bi-brightness-high fs-2"></i>
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                    <i class="bi bi-moon fs-2"></i>
                </a>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu item-->
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                    <i class="bi bi-display fs-2"></i>
                </a>
            </div>
            <!--end::Menu item-->
        </div>
        <!--end::Menu-->
    </div> --}}
</div>
VIEW;


        if ($this->updateViewFile($fileName['login'], $loginContent, $authPath)) {
            $this->info('Login file has been updated successfully!');
        } else {
            $this->error('Failed to update login file!');
        }

        if ($this->updateViewFile($fileName['register'], $registerContent, $authPath)) {
            $this->info('Register file has been updated successfully!');
        } else {
            $this->error('Failed to update register file!');
        }

        if ($this->updateViewFile($fileName['verify'], $verifyContent, $authPath)) {
            $this->info('Verify file has been updated successfully!');
        } else {
            $this->error('Failed to update verify file!');
        }

        if ($this->updateViewFile($fileName['email'], $emailContent, $passwordPath)) {
            $this->info('Email file has been updated successfully!');
        } else {
            $this->error('Failed to update email file!');
        }

        if ($this->updateViewFile($fileName['reset'], $resetContent, $passwordPath)) {
            $this->info('Reset file has been updated successfully!');
        } else {
            $this->error('Failed to update reset file!');
        }

        if ($this->updateViewFile($fileName['footer'], $footerContent, $footerPath)) {
            $this->info('Footer file has been updated successfully!');
        } else {
            $this->error('Failed to update footer file!');
        }
    }
}
