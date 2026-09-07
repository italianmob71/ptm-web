<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    private const RECIPIENT = 'janice@livingscroll.org';
    private const MIN_SUBMIT_SECONDS = 3;      // Bots submit near-instantly
    private const RATE_LIMIT_ATTEMPTS = 3;     // max submissions per window
    private const RATE_LIMIT_MINUTES  = 10;

    /**
     * Display the contact form.
     */
    public function show()
    {
        return view('contact.index', [
            'title' => 'Contact Us',
        ]);
    }

    /**
     * Handle a contact form submission.
     *
     * Anti-spam defenses (in order):
     *   1. Honeypot     — hidden field `website`; humans never fill it, bots often do.
     *   2. Time-trap    — forms rendered < MIN_SUBMIT_SECONDS ago are rejected.
     *   3. Rate limit   — 3 submissions per 10 minutes per IP.
     */
    public function submit(Request $request)
    {
        // --- 1. Honeypot -------------------------------------------------------
        // The "website" field is rendered off-screen with tabindex="-1"
        // and autocomplete="off". Real users never see or fill it.
        if ($request->filled('website')) {
            Log::info('Contact form honeypot triggered', [
                'ip' => $request->ip(),
                'ua' => substr((string) $request->userAgent(), 0, 200),
            ]);
            // Pretend success so bots think the message went through.
            return redirect()->route('contact')->with('success',
                'Thank you for your message. We will respond as soon as we can.');
        }

        // --- 2. Time-trap -----------------------------------------------------
        $renderedAt = (int) $request->input('_t', 0);
        $now = time();
        if ($renderedAt <= 0 || ($now - $renderedAt) < self::MIN_SUBMIT_SECONDS) {
            Log::info('Contact form time-trap triggered', [
                'ip'          => $request->ip(),
                'delta'       => $now - $renderedAt,
                'rendered_at' => $renderedAt,
            ]);
            return redirect()->route('contact')->with('error',
                'Your message was submitted too quickly. Please try again.');
        }

        // --- 3. Rate limit ----------------------------------------------------
        $key = 'contact-form:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, self::RATE_LIMIT_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->route('contact')->with('error',
                "You have sent too many messages recently. Please try again in " .
                ceil($seconds / 60) . " minute(s).");
        }

        // --- Validation --------------------------------------------------------
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email:rfc,dns', 'max:190'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'email.email' => 'Please provide a valid email address.',
            'message.min' => 'Your message must be at least 10 characters.',
        ]);

        // --- Send the message --------------------------------------------------
        try {
            Mail::raw(
                "New contact form submission from Living Scroll\n" .
                "================================================\n\n" .
                "Name:    {$validated['name']}\n" .
                "Email:   {$validated['email']}\n" .
                "Subject: {$validated['subject']}\n\n" .
                "Message:\n--------\n{$validated['message']}\n",
                function ($msg) use ($validated) {
                    $msg->to(self::RECIPIENT)
                        ->subject('[Living Scroll] ' . $validated['subject'])
                        ->replyTo($validated['email'], $validated['name']);
                }
            );
        } catch (\Throwable $e) {
            Log::error('Contact form mail send failed', [
                'error' => $e->getMessage(),
                'from'  => $validated['email'],
            ]);
            return redirect()->route('contact')->with('error',
                'There was a problem sending your message. Please try again later.');
        }

        RateLimiter::hit($key, self::RATE_LIMIT_MINUTES * 60);

        return redirect()->route('contact')->with('success',
            'Thank you for your message. We will respond as soon as we can.');
    }
}
