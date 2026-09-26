<?php

namespace Modules\Page\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Page\Database\Factories\PageFactory;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content', 'layout', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // protected static function newFactory(): PageFactory
    // {
    //     // return PageFactory::new();
    // }
}
