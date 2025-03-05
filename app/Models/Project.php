<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
        'description',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withTimestamps()
            ->withPivot('role');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function attributeValues()
    {
        return $this->morphMany(AttributeValue::class, 'entity', 'entity_type', 'entity_id');
    }

    public function getAttributeValue($attributeName)
    {
        $attribute = Attribute::where('name', $attributeName)->first();
        if (!$attribute) {
            return null;
        }

        $attributeValue = $this->attributeValues()
            ->where('attribute_id', $attribute->id)
            ->first();

        return $attributeValue ? $attributeValue->value : null;
    }

    public function setAttributeValue($project, $attributeName, $value)
    {
        if (!$project || !$project->exists) {
            return false;
        }

        $p = $project->toArray();

        $attribute = Attribute::firstOrCreate(['name' => $attributeName]);

        $attributeValue = AttributeValue::where('attribute_id', $attribute->id)
            ->where('entity_type', Project::class)
            ->where('entity_id', $p['id'])
            ->first();

        if ($attributeValue) {
            $attributeValue->update(['value' => $value]);
        } else {
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value' => $value,
                'entity_type' => Project::class,
                'entity_id' => $p['id']
            ]);
        }

        return true;
    }

    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $attributeName => $condition) {
            $query->whereHas('attributeValues', function ($q) use ($attributeName, $condition) {
                $q->whereHas('attribute', function ($attr) use ($attributeName) {
                    $attr->where('name', $attributeName);
                });

                if (is_array($condition)) {
                    $operator = $condition['operator'] ?? '=';
                    $value = $condition['value'] ?? null;

                    if ($value !== null) {
                        $q->where('value', $operator, $value);
                    }
                } else {
                    $q->where('value', $condition);
                }
            });
        }

        return $query;
    }
}
