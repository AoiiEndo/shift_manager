<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'availabilities';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'date',
        'organization_id',
    ];

    protected $dates = [
        'deleted_at', // 論理削除用のカラム
        'start_time', // 時間属性としての扱い
        'end_time',   // 時間属性としての扱い
        'date',       // 日付属性としての扱い
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
