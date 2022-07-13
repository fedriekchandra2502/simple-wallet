<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Withdrawal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'id' => 'string'
    ];

    protected static function booted()
    {
        static::creating(function($model) {
            $model->id = Str::uuid();
        });
    }

    public function by()
    {
        return $this->belongsTo(User::class, 'withdrawn_by');
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
