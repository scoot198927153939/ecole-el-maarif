<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoneyTransaction extends Model
{
    use HasFactory, SoftDeletes;

    public const MANUAL_CATEGORIES = [
        'in' => ['other_income'],
        'out' => ['supplies', 'utilities', 'maintenance', 'other_expense'],
    ];

    public const SYSTEM_CATEGORIES = ['fee_payment', 'refund', 'staff_advance', 'partner_withdrawal', 'salary'];

    protected $fillable = [
        'money_source_id',
        'direction',
        'amount',
        'description',
        'category',
        'transaction_date',
        'document_path',
        'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public static function categoriesFor(?string $direction): array
    {
        return self::MANUAL_CATEGORIES[$direction] ?? [];
    }

    public function isManual(): bool
    {
        return ! in_array($this->category, self::SYSTEM_CATEGORIES, true);
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category ? __('messages.treasury_category_'.$this->category) : __('messages.treasury_category_uncategorized');
    }

    public function moneySource()
    {
        return $this->belongsTo(MoneySource::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function logs()
    {
        return $this->hasMany(MoneyTransactionLog::class);
    }
}
