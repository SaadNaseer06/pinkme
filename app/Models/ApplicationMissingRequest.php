<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMissingRequest extends Model
{
    protected $fillable = [
        'application_id',
        'case_manager_id',
        'message',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function caseManager()
    {
        return $this->belongsTo(User::class, 'case_manager_id');
    }
}
