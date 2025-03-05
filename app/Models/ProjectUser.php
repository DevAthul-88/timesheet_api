<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectUser extends Pivot
{
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'role',
    ];

    const ROLE_MANAGER = 'manager';
    const ROLE_MEMBER = 'member';
    const ROLE_VIEWER = 'viewer';
}
