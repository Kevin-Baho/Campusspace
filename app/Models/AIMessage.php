<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIMessage extends Model
{
    protected $table = 'ai_messages';
    protected $fillable = ['ai_conversation_id', 'role', 'content'];

    public function conversation() { return $this->belongsTo(AIConversation::class, 'ai_conversation_id'); }
}
