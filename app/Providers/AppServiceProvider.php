<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
      
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function ($notifiable, $url) {
        //     return (new \Illuminate\Notifications\Messages\MailMessage)
        //         ->subject('Verify Email Address')
        //         ->line('Click the button below to verify your email address.')
        //         ->action('Verify Email Address', $url);
        // });
    }
    
} 
