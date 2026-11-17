<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingReview;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $this->authorize('review', $booking);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['nullable', 'string', 'max:1000']]);

        if ($booking->status !== 'completed') {
            throw ValidationException::withMessages(['rating' => 'Review hanya untuk booking yang sudah selesai.']);
        }
        if (BookingReview::where('booking_id', $booking->id)->exists()) {
            throw ValidationException::withMessages(['rating' => 'Booking ini sudah memiliki review.']);
        }
        BookingReview::create([
            'booking_id' => $booking->id, 'user_id' => $request->user()->id,
            'facility_id' => $booking->unit->facility_id, 'rating' => $data['rating'], 'comment' => $data['comment'] ?? null,
        ]);
        return back()->with('success', 'Terima kasih atas ulasan Anda.');
    }
}
