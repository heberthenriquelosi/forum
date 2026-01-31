<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo; 
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasUuids;

    protected $fillable = [
        'filename',
        'path',
        'mime_type',
        'size',
    ];


    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
