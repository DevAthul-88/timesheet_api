<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'options',
        'is_required',
        'entity_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    const TYPE_TEXT = 'text';
    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';
    const TYPE_SELECT = 'select';
    const TYPE_BOOLEAN = 'boolean';

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
