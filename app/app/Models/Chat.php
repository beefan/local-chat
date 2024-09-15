<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read User $user
 */
class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'user_id'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastMessages(?int $count = null): array
    {
        $count = $count ?? config('chat.lookbackMessages');
        return array_reverse($this->messages()
            ->orderByDesc('id')
            ->limit($count)
            ->get(['content', 'role'])
            ->toArray());
    }
}
