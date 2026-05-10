<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

final class LanguageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: URL param > Session > Browser > Default
        $locale = $this->getLocale($request);
        
        App::setLocale($locale);
        Session::put('locale', $locale);

        $response = $next($request);
        
        // Add locale to response headers
        $response->headers->set('X-Locale', $locale);

        return $response;
    }

    private function getLocale(Request $request): string
    {
        // From URL query param
        if ($request->query('lang')) {
            $locale = $request->query('lang');
            if (in_array($locale, config('app.supported_locales', ['en', 'bn']))) {
                return $locale;
            }
        }

        // From session
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // From browser accept-language
        if ($request->getPreferredLanguage(config('app.supported_locales', ['en', 'bn']))) {
            return $request->getPreferredLanguage(config('app.supported_locales', ['en', 'bn']));
        }

        // Default
        return config('app.locale', 'en');
    }
}
