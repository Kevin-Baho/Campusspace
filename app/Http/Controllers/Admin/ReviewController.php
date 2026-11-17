<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingReview;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Reviews', ['reviews' => BookingReview::with('user:id,name', 'facility:id,name')->latest()->paginate(25)]);
    }

    public function destroy(BookingReview $review)
    {
        $review->delete();
        return back()->with('success', 'Review dihapus.');
    }
}
