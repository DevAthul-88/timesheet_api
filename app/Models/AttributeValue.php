<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_id',
        'entity_id',
        'entity_type',
        'value',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
