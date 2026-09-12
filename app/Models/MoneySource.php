<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneySource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function transactions()
    {
        return $this->hasMany(MoneyTransaction::class);
    }

    // الرصيد الحالي = مجموع كل الإيداعات ناقص مجموع كل السحوبات
    public function balance(): float
    {
        $in = $this->transactions()->where('direction', 'in')->sum('amount');
        $out = $this->transactions()->where('direction', 'out')->sum('amount');

        return $in - $out;
    }
}