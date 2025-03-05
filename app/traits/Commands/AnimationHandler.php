<?php

namespace App\Traits\Commands;

trait AnimationHandler
{
    /**
     * Shows a loading animation with custom message
     *
     * @param string $message The message to display during animation
     * @param int $duration Duration for each frame in microseconds
     * @return void
     */
    protected function showLoadingAnimation(string $message = 'Loading next command', int $duration = 100000)
    {
        $frames = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
        foreach ($frames as $frame) {
            echo "\r$frame $message...";
            usleep($duration);
        }
        echo "\r"; // Clear the loading animation
        echo str_repeat(' ', strlen($message) + 25) . "\r"; // Clear any remaining characters
    }

    /**
     * Adds a delay in execution
     *
     * @param int $microseconds Time to delay in microseconds
     * @return void
     */
    protected function addDelay(int $microseconds = 500000)
    {
        usleep($microseconds);
    }

    /**
     * Shows a spinner animation
     *
     * @param string $message The message to display during animation
     * @param int $duration Duration for each frame in microseconds
     * @return void
     */
    protected function showSpinner(string $message = 'Processing', int $duration = 100000)
    {
        $spinners = ['◐', '◓', '◑', '◒'];
        foreach ($spinners as $spinner) {
            echo "\r$spinner $message...";
            usleep($duration);
        }
        echo "\r" . str_repeat(' ', strlen($message) + 25) . "\r";
    }

    /**
     * Shows a progress dots animation
     *
     * @param string $message The message to display during animation
     * @param int $duration Duration for each frame in microseconds
     * @return void
     */
    protected function showProgressDots(string $message = 'Loading', int $duration = 300000)
    {
        $dots = ['⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏', '⠋', '⠙', '⠹'];
        foreach ($dots as $dot) {
            echo "\r$message $dot";
            usleep($duration);
        }
        echo "\r" . str_repeat(' ', strlen($message) + 25) . "\r";
    }
}
