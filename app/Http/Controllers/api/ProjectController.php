<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProjectController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $filters = $request->query('filter', []);

            $allowedColumns = Schema::getColumnListing((new Project)->getTable());

            $projectsQuery = QueryBuilder::for(Project::class)
                ->allowedFilters(array_merge($allowedColumns, [
                    AllowedFilter::callback('eav', function ($query, $value) {
                        if (!is_array($value)) {
                            Log::warning('EAV filter value is not an array');
                            return $query;
                        }

                        foreach ($value as $attribute => $filterValue) {
                            $query->whereHas('attributeValues', function ($query) use ($attribute, $filterValue) {
                                $query->whereHas('attribute', function ($subQuery) use ($attribute) {
                                    $subQuery->where('name', $attribute);
                                })->where('value', $filterValue);
                            });
                        }

                        return $query;
                    }),
                ]))
                ->allowedSorts($allowedColumns);

            if (!empty($filters)) {
                foreach ($filters as $key => $value) {
                    Log::info('Applying Standard Filter', ['key' => $key, 'value' => $value]);
                }
            }

            $projects = $projectsQuery->paginate($perPage);

            $projects->getCollection()->transform(function ($project) {
                $projectId = $project->id;

                $users = User::whereHas('projects', fn($query) => $query->where('projects.id', $projectId))->get();
                $timesheets = Timesheet::where('project_id', $projectId)->get();

                $attributeValues = AttributeValue::where('entity_id', $projectId)
                    ->get()
                    ->map(function ($attributeValue) {
                        $attributeValue->attribute = Attribute::find($attributeValue->attribute_id);
                        $attributeValue->attribute->options = json_decode($attributeValue->attribute->options, true) ?? [];
                        return $attributeValue;
                    });

                return array_merge($project->toArray(), [
                    'users' => $users,
                    'timesheets' => $timesheets,
                    'attributeValues' => $attributeValues,
                ]);
            });

            return response()->json($projects);
        } catch (\Throwable $e) {
            Log::error('Project Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->handleException($e, 'Failed to fetch projects.');
        }
    }

    public function show($id): JsonResponse
    {
        try {

            $project = Project::findOrFail($id);

            $users = User::whereHas('projects', function ($query) use ($id) {
                $query->where('projects.id', $id);
            })->get();

            $timesheets = Timesheet::where('project_id', $id)->get();

            $attributeValues = AttributeValue::where('entity_id', $id)
            ->get()
            ->map(function ($attributeValue) {
                $attributeValue->attribute = Attribute::find($attributeValue->attribute_id);

                if ($attributeValue->attribute && $attributeValue->attribute->options) {
                    $options = json_decode($attributeValue->attribute->options, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $options = [];
                    }

                    $attributeValue->attribute->options = $options;
                } else {
                    $attributeValue->attribute->options = [];
                }

                return $attributeValue;
            });

            $project->setAttribute('users', $users);

            $project->setAttribute('timesheets', $timesheets);


            $project->setAttribute('attributeValues', $attributeValues);

            return response()->json(new ProjectResource($project));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Project not found.'], 404);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Failed to fetch project.');
        }
    }


    public function store(StoreProjectRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $projectData = $request->validated();
            $attributes = $request->input('attributes', []);
            foreach ($attributes as $attributeName => $value) {
                $attribute = Attribute::where('name', $attributeName)->first();
                if (!$attribute) {
                    return response()->json(['message' => "Attribute '$attributeName' does not exist"], 400);
                }
            }

            $project = Project::create($projectData);

            $project->refresh();

            $this->updateAttributes($project, $attributes);

            $projectId = $project->toArray()["id"];

            $users = User::whereHas('projects', function ($query) use ($projectId) {
                $query->where('projects.id', $projectId);
            })->get();

            $timesheets = Timesheet::where('project_id', $projectId)->get();

            $attributeValues = AttributeValue::where('entity_id', $projectId)
                ->get()
                ->map(function ($attributeValue) {
                    $attributeValue->attribute = Attribute::find($attributeValue->attribute_id);

                    if ($attributeValue->attribute && $attributeValue->attribute->options) {
                        $options = json_decode($attributeValue->attribute->options, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $options = [];
                        }

                        $attributeValue->attribute->options = $options;
                    } else {
                        $attributeValue->attribute->options = [];
                    }

                    return $attributeValue;
                });



            $project->setAttribute('users', $users);
            $project->setAttribute('timesheets', $timesheets);
            $project->setAttribute('attributeValues', $attributeValues);

            DB::commit();

            return response()->json(new ProjectResource($project), 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Project creation failed:', ['error' => $e->getMessage()]);
            return $this->handleException($e, 'Failed to create project.');
        }
    }

    public function update(UpdateProjectRequest $request, $project): JsonResponse
    {
        DB::beginTransaction();
        try {
            $project = Project::findOrFail($project);

            $attributes = $request->input('attributes', []);
            foreach ($attributes as $attributeName => $value) {
                $attribute = Attribute::where('name', $attributeName)->first();
                if (!$attribute) {
                    return response()->json(['message' => "Attribute '$attributeName' does not exist"], 400);
                }
            }

            $project->update($request->validated());

            $project->refresh();

            $this->updateAttributes($project, $attributes);

            $projectId = $project->toArray()["id"];

            $users = User::whereHas('projects', function ($query) use ($projectId) {
                $query->where('projects.id', $projectId);
            })->get();


            $timesheets = Timesheet::where('project_id', $projectId)->get();

            $attributeValues = AttributeValue::where('entity_id', $projectId)
                ->get()
                ->map(function ($attributeValue) {
                    $attributeValue->attribute = Attribute::find($attributeValue->attribute_id);

                    if ($attributeValue->attribute && $attributeValue->attribute->options) {
                        $options = json_decode($attributeValue->attribute->options, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $options = [];
                        }

                        $attributeValue->attribute->options = $options;
                    } else {
                        $attributeValue->attribute->options = [];
                    }

                    return $attributeValue;
                });

            $project->setAttribute('users', $users);
            $project->setAttribute('timesheets', $timesheets);
            $project->setAttribute('attributeValues', $attributeValues);

            DB::commit();

            return response()->json(new ProjectResource($project));
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Project not found.'], 404);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Project update failed:', ['error' => $e->getMessage()]);
            return $this->handleException($e, 'Failed to update project.');
        }
    }

    public function destroy($project): JsonResponse
    {
        DB::beginTransaction();
        try {
            $project = Project::withTrashed()->findOrFail($project);

            Timesheet::where('project_id', $project)->delete();
            AttributeValue::where('entity_id', $project)->delete();

            $project->users()->detach();

            $project->delete();

            DB::commit();

            return response()->json(['message' => 'Project deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Project not found.'], 404);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Project deletion failed:', ['error' => $e->getMessage()]);
            return $this->handleException($e, 'Failed to delete project.');
        }
    }


    public function setAttributes(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $project = Project::findOrFail($id);
            $this->updateAttributes($project, $request->input('attributes', []));

            DB::commit();
            return response()->json(['message' =>    'Attributes updated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to set attributes.');
        }
    }

    public function getAttributes($id): JsonResponse
    {
        try {
            $project = Project::findOrFail($id);
            $attributes = $project->attributeValues->map(fn($attributeValue) => [
                'name' => $attributeValue->attribute->name,
                'value' => $attributeValue->value,
            ]);

            return response()->json($attributes);
        } catch (\Throwable $e) {
            return $this->handleException($e, 'Failed to fetch attributes.');
        }
    }

    private function updateAttributes(Project $project, array $attributes): void
    {
        foreach ($attributes as $attributeName => $value) {
            $project->setAttributeValue($project, $attributeName, $value);
        }
    }

    public function assignUser(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $project = Project::findOrFail($id);

            $userId = $request->input('user_id');
            $role = $request->input('role', 'member');

            ProjectUser::create([
                'project_id' => $id,
                'user_id' => $userId,
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            return response()->json(['message' => 'User assigned successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to assign user.');
        }
    }
    public function removeUser(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $project = Project::findOrFail($id);

            $userId = $request->input('user_id');

            ProjectUser::where('project_id', $id)
                ->where('user_id', $userId)
                ->delete();

            DB::commit();
            return response()->json(['message' => 'User removed successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->handleException($e, 'Failed to remove user.');
        }
    }


    private function handleException(\Throwable $e, string $message): JsonResponse
    {
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'message' => $message,
                'errors' => $e->validator->errors()->toArray()
            ], 422);
        }

        Log::error($message, ['exception' => $e]);

        return response()->json(['message' => $message, 'error' => $e->getMessage()], 500);
    }
}
