<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id',
        'filename',
        'filepath',
        'filetype',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
