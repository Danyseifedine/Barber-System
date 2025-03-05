<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;


class BaseController extends Controller
{
    /**
     * Create a success response.
     *
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    protected function successResponse($data = [], $status = 200): JsonResponse
    {
        $response = ['success' => true];
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        return response()->json($response, $status);
    }

    /**
     * Create an error response.
     *
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function errorResponse($message, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

    /**
     * Create a success response with redirect.
     *
     * @param string $redirect
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    protected function successRedirectResponse($redirect, $data = [], $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'redirect' => $redirect
        ];
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        return response()->json($response, $status);
    }

    /**
     * Create an error response with redirect.
     *
     * @param string $redirect
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function errorRedirectResponse($redirect, $message, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'redirect' => $redirect,
            'message' => $message
        ], $status);
    }

    /**
     * Create a success response with a toast message.
     *
     * @param string $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    protected function successToastResponse($message, $data = [], $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'toast' => [
                'type' => 'success',
                'message' => $message
            ]
        ];
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        return response()->json($response, $status);
    }

    /**
     * Create an error response with a toast message.
     *
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function errorToastResponse($message, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'toast' => [
                'type' => 'error',
                'message' => $message
            ]
        ], $status);
    }

    /**
     * Create a success response with both redirect and toast message.
     *
     * @param string $redirect
     * @param string $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    protected function successRedirectToastResponse($redirect, $message, $data = [], $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'redirect' => $redirect,
            'toast' => [
                'type' => 'success',
                'message' => $message
            ]
        ];
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        return response()->json($response, $status);
    }

    /**
     * Create an error response with both redirect and toast message.
     *
     * @param string $redirect
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function errorRedirectToastResponse($redirect, $message, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'redirect' => $redirect,
            'toast' => [
                'type' => 'error',
                'message' => $message
            ]
        ], $status);
    }

    /**
     * Create a response for form validation errors.
     *
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    protected function validationErrorResponse($errors, $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $errors
        ], $status);
    }

    /**
     * Create a response for tab content.
     *
     * @param View|string $view
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    protected function tabContentResponse($view, $data = [], $status = 200): JsonResponse
    {
        if ($view instanceof View) {
            $content = $view->render();
        } else {
            $content = $view;
        }

        $response = [
            'success' => true,
            'html' => $content,
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $status);
    }

    protected function modalToastResponse($message, $data = [], $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'closeModal' => true,
            'toast' => [
                'type' => 'success',
                'message' => $message
            ]
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        return response()->json($response, $status);
    }

    protected function componentResponse($component, $data = [], $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'html' => $component->render()
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $status);
    }
}
