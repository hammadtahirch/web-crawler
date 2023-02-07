<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvgReport extends Model
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
        'site_link',
        'avg_page_load_time',
        'avg_title_length',
        'avg_world_count',
        'crawled_pages',
        'created_at',
        'updated_at',
    ];
}
