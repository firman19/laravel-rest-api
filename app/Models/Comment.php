<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];
    protected $hidden = ['deleted_at', 'blog_id'];
    protected $appends = array('total_like', 'total_dislike', 'has_liked', 'name');

    protected $fillable = [
        'comment', 'user_id', 'blog_id',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

     public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getNameAttribute()
    {
        return $this->users()->value('name');
    }

    public function getTotalLikeAttribute()
    {
        return $this->hasMany(CommentLike::class)->where('status', 1)->count();
    }

    public function getTotalDislikeAttribute()
    {
        return $this->hasMany(CommentLike::class)->where('status', -1)->count();
    }

    public function getHasLikedAttribute()
    {
        return $this->hasMany(CommentLike::class)
            ->select('status')
            // to be updated
            ->where('user_id', 1)
            ->value('status');
    }   
}
