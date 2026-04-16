<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'title', 'description', 'file_path', 'due_date', 'max_grade'];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
