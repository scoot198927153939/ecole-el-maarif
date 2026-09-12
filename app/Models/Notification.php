<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id',
        'changed_by',
        'old_score',
        'new_score',
        'message',
        'is_read',
    ];

    protected $casts = [
        'old_score' => 'decimal:2',
        'new_score' => 'decimal:2',
        'is_read' => 'boolean',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}