<?php

namespace App\Http\Controllers;

use App\Models\AIConversation;
use App\Services\GroqAIService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AIController extends Controller
{
    public function index(Request $request)
    {
        $conv = AIConversation::where('user_id', $request->user()->id)->latest('updated_at')->first();
        return Inertia::render('AI/Chat', [
            'messages' => $conv ? $conv->messages()->orderBy('id')->get(['id', 'role', 'content']) : [],
        ]);
    }

    public function message(Request $request, GroqAIService $ai)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:1000']]);
        $user = $request->user();
        $conv = AIConversation::firstOrCreate(['user_id' => $user->id], ['title' => 'Percakapan']);
        $conv->messages()->create(['role' => 'user', 'content' => $data['message']]);

        $history = $conv->messages()->orderByDesc('id')->limit(12)->get(['role', 'content'])->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])->values()->all();

        try {
            $reply = $ai->reply($user, $history);
            if ($reply === '') throw new \RuntimeException('Jawaban kosong.');
        } catch (\Throwable $e) {
            report($e);
            $reply = 'Maaf, AI Assistant sedang tidak dapat dihubungi. Anda tetap bisa memesan fasilitas lewat menu Library, Saung, atau Room.';
        }
        $conv->messages()->create(['role' => 'assistant', 'content' => $reply]);
        $conv->touch();
        return back();
    }

    public function clear(Request $request)
    {
        AIConversation::where('user_id', $request->user()->id)->delete();
        return back();
    }
}
