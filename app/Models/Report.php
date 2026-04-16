<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reportable_id', 'reportable_type', 'reason', 'status', 'notes'];

    // Para poder reportar tanto una Investigación (Post) como un Comentario.
    public function reportable()
    {
        return $this->morphTo();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
