<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /**
     * This trait allows the model to use the built-in factory to create new instances.
     */
    use HasFactory;

    /**
     * Models fillables and representation of table columns
     */
    protected $fillable = [
        'email',
        'page_link',
        'status_code',
        'images_links',
        'internal_links',
        'external_links',
        'page_load_time',
        'word_count',
        'title_length',
        'created_at',
        'updated_at',
    ];
}
