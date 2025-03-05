<?php

namespace App\Console\Commands\Setup\NotTranslation\Auth\Update;

use App\Traits\Commands\CssFileHandler;
use Illuminate\Console\Command;

class AppCssCommand extends Command
{

    use CssFileHandler;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:app-css-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update the css file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appCssContent = <<< CSS
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Noto+Kufi+Arabic:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Rubik", sans-serif;
}

.app-content {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
}

[data-bs-theme="dark"] .card-bg {
    background-color: #121212;
    box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
}

[data-bs-theme="light"] .card-bg {
    background-color: rgb(255, 255, 255);
    box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
}

:lang(ar) {
    font-family: "Changa", sans-serif !important;
}

:lang(ar) {
    font-family: "Changa", sans-serif !important;
}

:lang(ar) .iziToast {
    font-family: "Changa", sans-serif !important;
}

:lang(ar) .iziToast-title {
    font-weight: bolder !important;
    font-size: 16px !important;
    font-family: "Changa", sans-serif !important;
}

:lang(ar) .iziToast-message {
    font-size: 14px !important;
    font-weight: 500 !important;
    font-family: "Changa", sans-serif !important;
}

.iziToast {
    font-family: Inter, Helvetica, sans-serif !important;
}

.iziToast-title {
    font-weight: bolder !important;
    font-size: 16px !important;
    font-family: Inter, Helvetica, sans-serif !important;
}

.iziToast-message {
    font-size: 14px !important;
    font-weight: 500 !important;
    font-family: Inter, Helvetica, sans-serif !important;
}

.dropzone.empty {
    border: 2px dashed #ff4d4d;
    padding: 20px;
    border-radius: 5px;
}

.dropzone .dz-message {
    color: #ff4d4d;
}

.dropzone .dz-message:hover {
    color: #cc0000;
}

.dropzone .form-control form-control-solid-icon {
    color: #ff4d4d;
}

.select2-selection__choice__remove {
    right: auto;
    left: 5px;
}

.devToolContainer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #1e1e1e;
    color: #e0e0e0;
    font-family: 'Arial', sans-serif;
    z-index: 10000;
    transition: all 0.3s ease;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.devToolHeader {
    padding: 10px 20px;
    background-color: #2d2d2d;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.devToolHeader h3 {
    margin: 0;
    font-size: 18px;
    color: #4CAF50;
}

.devToolToggle {
    background: none;
    border: none;
    color: #e0e0e0;
    font-size: 20px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.devToolContent {
    padding: 20px;
    max-height: 800px;
    overflow-y: auto;
}

.devToolTabs {
    display: flex;
    margin-bottom: 20px;
}

.devToolTabButton {
    background-color: #2d2d2d;
    border: none;
    color: #e0e0e0;
    padding: 10px 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.devToolTabButton.active {
    background-color: #4CAF50;
}

.devToolTabContent {
    display: none;
}

.devToolTabContent.active {
    display: block;
}

.devToolSection {
    margin-bottom: 20px;
}

.devToolSection h4 {
    margin-top: 0;
    color: #4CAF50;
}

.devToolList {
    list-style-type: none;
    padding: 0;
}

.devToolList li {
    margin-bottom: 5px;
}

.devToolSelect {
    width: 100%;
    padding: 5px;
    margin-bottom: 10px;
    background-color: #2d2d2d;
    color: #e0e0e0;
    border: 1px solid #4CAF50;
}

.devToolDocumentation {
    background-color: #2d2d2d;
    padding: 10px;
    border-radius: 4px;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.bg-logo {
    background-color: #F77E15 !important;
    color: white !important;
}

.color-logo {
    color: #F77E15 !important;
}

.bg-logo:hover {
    background-color: #cc6d00 !important;
    color: white !important;
}

.logo-border {
    border: 1px solid #F77E15 !important;
}

.logo-border:hover {
    border: 1px solid #cc6d00 !important;
}

.text-logo {
    color: #F77E15 !important;
}

.text-hover:hover {
    color: #F77E15 !important;
}

.auth {
    background-image: url('/core/vendor/img/bg/LEBIFY-light-1.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: bottom;
}

[data-bs-theme="dark"] .auth {
    background-image: url('/core/vendor/img/bg/LEBIFY-dark-1.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: bottom;
}

.languageBox {
    background-color: white;
}

[data-bs-theme="dark"] .languageBox {
    background-color: rgb(26, 26, 26);
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

[data-bs-theme="dark"] .custom-title-class-delete {
    color: #ffffff !important;
}

.custom-title-class-delete {
    font-size: 16px !important;
    font-weight: bold !important;
}

.custom-confirm-button-class-delete {
    background-color: #dc2626 !important;
    color: #ffffff !important;
}

.custom-cancel-button-class-delete {
    background-color: #333333 !important;
    color: #ffffff !important;
}

[data-bs-theme="dark"] .custom-title-class-success-delete {
    color: #ffffff !important;
}

.custom-confirm-button-class-success-delete {
    background-color: #28a745 !important;
    color: #ffffff !important;
}

.swal2-icon.swal2-success .swal2-success-ring {
    border-color: transparent !important;
}

.swal2-icon.swal2-warning .swal2-warning-ring {
    border-color: transparent !important;
}

[data-bs-theme="dark"] .custom-title-class-error {
    color: #ffffff !important;
}

.custom-confirm-button-class-error {
    background-color: #E42855 !important;
    color: #ffffff !important;
}

.datatable-btn {
    transition: all 0.3s ease;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.datatable-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.data-table-action-edit {
    background-color: #0d6dfd1d !important;
    box-shadow: 0 2px 4px rgba(13, 109, 253, 0.2), 0 1px 2px rgba(13, 109, 253, 0.1) !important;
    transition: all 0.3s ease;
}

.data-table-action-edit:hover {
    background-color: #0d6dfd61 !important;
    color: #fff !important;
    box-shadow: 0 4px 8px rgba(13, 109, 253, 0.3), 0 2px 4px rgba(13, 109, 253, 0.2) !important;
    transform: translateY(-1px);
}

.data-table-action-show {
    background-color: #1987541d !important;
    box-shadow: 0 2px 4px rgba(25, 135, 84, 0.2), 0 1px 2px rgba(25, 135, 84, 0.1) !important;
    transition: all 0.3s ease;
}

.data-table-action-show:hover {
    background-color: #19875461 !important;
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3), 0 2px 4px rgba(25, 135, 84, 0.2) !important;
    transform: translateY(-1px);
}

.data-table-action-delete {
    background-color: #dc35451d !important;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2), 0 1px 2px rgba(220, 53, 69, 0.1) !important;
    transition: all 0.3s ease;
}

.data-table-action-delete:hover {
    background-color: #dc354561 !important;
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3), 0 2px 4px rgba(220, 53, 69, 0.2) !important;
    transform: translateY(-1px);
}

.select-info {
    display: none !important;
}

.datatable-body {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.card-error {
    background: rgba(255, 255, 255, 0.3) !important;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37) !important;
    backdrop-filter: blur(9.5px) !important;
    -webkit-backdrop-filter: blur(9.5px) !important;
    border-radius: 10px !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
}

[data-bs-theme="light"] .skeleton-wrapper {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 4px;
}

[data-bs-theme="light"] .skeleton-header,
[data-bs-theme="light"] .skeleton-text,
[data-bs-theme="light"] .skeleton-button {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

[data-bs-theme="light"] .skeleton-header {
    height: 30px;
    margin-bottom: 20px;
    width: 50%;
}

[data-bs-theme="light"] .skeleton-text {
    height: 20px;
    margin-bottom: 15px;
}

[data-bs-theme="light"] .skeleton-text:last-of-type {
    width: 80%;
}

[data-bs-theme="light"] .skeleton-button {
    height: 35px;
    width: 120px;
    border-radius: 4px;
}

[data-bs-theme="dark"] .skeleton-wrapper {
    padding: 20px;
    background: rgb(21, 23, 28);
    border-radius: 4px;
}

[data-bs-theme="dark"] .skeleton-header,
[data-bs-theme="dark"] .skeleton-text,
[data-bs-theme="dark"] .skeleton-button {
    background: linear-gradient(90deg, rgb(31, 33, 38) 25%, rgb(41, 43, 48) 50%, rgb(31, 33, 38) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

[data-bs-theme="dark"] .skeleton-header {
    height: 30px;
    margin-bottom: 20px;
    width: 50%;
}

[data-bs-theme="dark"] .skeleton-text {
    height: 20px;
    margin-bottom: 15px;
}

[data-bs-theme="dark"] .skeleton-text:last-of-type {
    width: 80%;
}

[data-bs-theme="dark"] .skeleton-button {
    height: 35px;
    width: 120px;
    border-radius: 4px;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
}

@media (max-width:768px) {
    .dt-toolbar {
        margin-top: 50px !important;
    }
}

@media (max-width:576px) {
    .datatable-body {
        gap: 10px;
        flex-direction: column !important;
    }
}

.lebify-scale-up {
    transition: transform 0.2s ease-in-out;
}

.lebify-scale-up:hover {
    transition: transform 0.2s ease-in-out;
    transform: scale(1.02);
}

.lebify-scale-up:not(:hover) {
    transform: scale(1);
}

.lebify-fade-in {
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.lebify-slide-in-left:hover {
    animation: slideInLeft 0.5s ease-out;
}

@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
    }

    to {
        transform: translateX(0);
    }
}

.lebify-bounce:hover {
    animation: bounce 0.5s ease;
}

@keyframes bounce {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-10px);
    }
}

.lebify-rotate:hover {
    animation: rotate 2s linear;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.lebify-pulse:hover {
    animation: pulse 1s ease;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
    }
}

.lebify-shake:hover {
    animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
}

@keyframes shake {

    10%,
    90% {
        transform: translate3d(-1px, 0, 0);
    }

    20%,
    80% {
        transform: translate3d(2px, 0, 0);
    }

    30%,
    50%,
    70% {
        transform: translate3d(-4px, 0, 0);
    }

    40%,
    60% {
        transform: translate3d(4px, 0, 0);
    }
}
CSS;

        $minify = $this->minifyCss($appCssContent);

        if ($this->updateCssFile('app.css', $minify)) {
            $this->info('update css file success');
        } else {
            $this->info('update css file failed');
        }
    }
}
