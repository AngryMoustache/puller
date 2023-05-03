<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    protected $fillable = [
        'original_name',
        'alt_name',
        'preview_id',
        'extension',
        'size',
    ];

    public $with = [
        'preview',
    ];

    public function preview()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function fullPath()
    {
        return Storage::path("public/videos/{$this->id}/{$this->original_name}");
    }

    public function path()
    {
        return Storage::url("public/videos/{$this->id}/{$this->original_name}");
    }
}
