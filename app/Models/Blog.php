<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at', 'updated_at', 'created_at', 'published_date'];
    protected $hidden = ['deleted_at'];
    protected $appends = array('total_comment', 'total_like', 'total_dislike', 'has_liked', 'author');

    protected $fillable = [
        'title', 'user_id', 'body', 'status', 'published_date'
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getAuthorAttribute()
    {
        return $this->users()->value('name');
    }

    public function getTotalCommentAttribute()
    {
        return $this->hasMany(Comment::class)->count();
    }

    public function getTotalLikeAttribute()
    {
        return $this->hasMany(BlogLike::class)->where('status', 1)->count();
    }

    public function getTotalDislikeAttribute()
    {
        return $this->hasMany(BlogLike::class)->where('status', -1)->count();
    }

    public function getHasLikedAttribute()
    {
        $user_id = Auth::user()->id;
        return $this->hasMany(BlogLike::class)
            ->select('status')
            // to be updated
            ->where('user_id', $user_id)
            ->value('status');
    }
}
