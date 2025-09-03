<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Mail\BookingNotification;
use App\Mail\ContactNotification;

class PhotographyController extends Controller
{
    public function sendContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Send email notification
        try {
            Mail::to(env('MAIL_TO_ADDRESS', 'fotomimi@gmail.com'))
                ->send(new ContactNotification($request->all()));
            
            return back()->with('success', 'Thank you for your message! We will get back to you soon.');
        } catch (\Exception $e) {
            // Log the error but still show success to user
            \Log::error('Failed to send contact email: ' . $e->getMessage());
            
            return back()->with('success', 'Thank you for your message! We will get back to you soon.');
        }
    }

    public function sendBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'event_type' => 'required|string|in:wedding,baptism,concert,on-set,studio,modelling,travel,video,other',
            'event_date' => 'required|date|after:today',
            'location' => 'required|string|max:255',
            'guests' => 'nullable|integer|min:1',
            'budget' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Send email notification
        try {
            Mail::send(new BookingNotification($request->all()));
            
            return back()->with('success', 'Thank you for your booking request! We will contact you soon to discuss the details.');
        } catch (\Exception $e) {
            // Log the error but still show success to user
            \Log::error('Failed to send booking email: ' . $e->getMessage());
            
            return back()->with('success', 'Thank you for your booking request! We will contact you soon to discuss the details.');
        }
    }
} 