<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleComment extends Model
{
    use HasFactory;

    protected $table = 'article_comments';

    protected $fillable = [
        'article_id',
        'user_id',
        'komentar',
        'is_approved',
        'isActive',
        'isDelete',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'isActive' => 'boolean',
        'isDelete' => 'boolean',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
