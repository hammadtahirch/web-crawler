<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvgReport extends Model
{
    /**
     * trait
     */
    use HasFactory;

    /**
     * Models fillables and representation of table columns
     */
    protected $fillable = [
        'site_link',
        'avg_page_load_time',
        'avg_title_length',
        'avg_world_count',
        'crawled_pages',
        'created_at',
        'updated_at',
    ];
}
