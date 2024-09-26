<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prompt',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function default(): ?self
    {
        return self::where(['name' => 'default'])->first();
    }
}
