<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class TimesheetResource extends JsonResource
{
    public function toArray($request)
    {
        $projectArray = $this->project ? $this->project->toArray() : null;

        return [
            'id' => $this->id,
            'task_name' => $this->task_name,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'hours' => $this->hours,
            'description' => $this->description,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->first_name . ' ' . $this->user->last_name,
            ] : null,
            'project' => $this->project ? [
                'id' => $projectArray['id'],
                'name' => $projectArray['name'],
            ] : null,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
