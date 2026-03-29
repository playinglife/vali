<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Email address to send to (you can change this)
        $toEmail = env('CONTACT_EMAIL', 'info@clef.com');

        // Send the email
        Mail::raw(
            "Name: {$validated['name']}\n" .
            "Email: {$validated['email']}\n\n" .
            "Message:\n{$validated['message']}",
            function ($message) use ($validated, $toEmail) {
                $message->to($toEmail)
                    ->subject('New Contact Form Submission')
                    ->replyTo($validated['email'], $validated['name']);
            }
        );

        // Redirect to success page
        return redirect()->route('contact.success')->with('success', 'Your message has been sent successfully. We typically respond within 24 hours.');
    }
}

