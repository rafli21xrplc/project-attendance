<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class type_payment extends Model
{
    use HasFactory;
    protected $table = 'type_payments';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'name',
    ];
    protected $primaryKey = 'id';
    public $incrementing = false;
}
