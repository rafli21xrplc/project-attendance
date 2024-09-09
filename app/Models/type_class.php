<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class type_class extends Model
{
    use HasFactory;
    protected $table = 'type_class';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'category',
    ];
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function classrooms()
    {
        return $this->hasMany(classRoom::class, 'type_class_id')
            ->orderByRaw("FIELD(name, 'METRO A', 'METRO B', 'ELIN A', 'ELIN B', 'RPL A', 'RPL B', 'RPL C', 'RPL D', 'TKJ A', 'TKJ B', 'TKJ C', 'TKJ D')");
    }
}
