<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        try {
            // 1️⃣ Validate request
            $data = $request->validate([
                'name'    => 'required|string|max:100',
                'email'   => 'required|email',
                'subject' => 'required|string|max:150',
                'message' => 'required|string',
            ]);

            // 2️⃣ Save message to database
            ContactMessage::create($data);
            

            // 3️⃣ Send email to admin
            Mail::to('sumaanshakeel@gmail.com')->send(
                new ContactMessageMail($data)
            );

            // 4️⃣ AJAX success response
            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! We will contact you soon.'
            ]);

        } catch (ValidationException $e) {
            // 5️⃣ Validation error response
            return response()->json([
                'success' => false,
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // 6️⃣ Any other error (SMTP, DB, etc.)
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
