<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Timesheet\StoreTimesheetRequest;
use App\Http\Requests\Timesheet\UpdateTimesheetRequest;
use App\Http\Resources\TimesheetResource;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TimesheetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $sortBy = $request->query('sort_by', 'created_at');
            $sortDirection = $request->query('sort_direction', 'desc');

            $allowedSortColumns = [
                'id',
                'task_name',
                'date',
                'hours',
                'created_at',
                'updated_at'
            ];

            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }

            $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc'])
                ? $sortDirection
                : 'desc';

            $query = Timesheet::with(['user', 'project'])
                ->when($request->query('task_name'), function ($q, $taskName) {
                    return $q->where('task_name', 'like', "%{$taskName}%");
                })
                ->when($request->query('project_id'), function ($q, $projectId) {
                    return $q->where('project_id', $projectId);
                })
                ->when($request->query('user_id'), function ($q, $userId) {
                    return $q->where('user_id', $userId);
                })
                ->when($request->query('date_from'), function ($q, $dateFrom) {
                    return $q->where('date', '>=', $dateFrom);
                })
                ->when($request->query('date_to'), function ($q, $dateTo) {
                    return $q->where('date', '<=', $dateTo);
                })
                ->orderBy($sortBy, $sortDirection);

            $timesheets = $query->paginate($perPage);

            return response()->json([
                'data' => TimesheetResource::collection($timesheets),
                'meta' => [
                    'current_page' => $timesheets->currentPage(),
                    'total_pages' => $timesheets->lastPage(),
                    'total_items' => $timesheets->total(),
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching timesheets: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch timesheets'], 500);
        }
    }

    public function store(StoreTimesheetRequest $request): JsonResponse
    {
        try {
            $timesheet = Timesheet::create($request->validated());

            $timesheet->user = User::find($timesheet->user_id);
            $timesheet->project = Project::find($timesheet->project_id);
            return response()->json(new TimesheetResource($timesheet), 201);
        } catch (\Exception $e) {
            Log::error('Error creating timesheet: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create timesheet'], 500);
        }
    }

    public function show(Timesheet $timesheet): JsonResponse
    {
        try {
            $timesheet = Timesheet::with(['user', 'project'])
                ->findOrFail($timesheet->id);

            $timesheet->project = Project::find($timesheet->project_id);

            return response()->json(new TimesheetResource($timesheet));
        } catch (\Exception $e) {
            Log::error('Error fetching timesheet: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch timesheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(UpdateTimesheetRequest $request, Timesheet $timesheet): JsonResponse
    {
        try {
            $timesheet->update($request->validated());
            $timesheet->user = User::find($timesheet->user_id);
            $timesheet->project = Project::find($timesheet->project_id);

            return response()->json(new TimesheetResource($timesheet));
        } catch (\Exception $e) {
            Log::error('Error updating timesheet: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update timesheet'], 500);
        }
    }

    public function getTimesheetsForCurrentUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $timesheets = Timesheet::where('user_id', $user->id)
                ->with(['user', 'project'])
                ->paginate(10);

            return response()->json(TimesheetResource::collection($timesheets));
        } catch (\Exception $e) {
            Log::error('Error fetching timesheets: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch timesheets'], 500);
        }
    }

    public function destroy(Timesheet $timesheet): JsonResponse
    {
        try {
            $timesheet->delete();
            return response()->json(['message' => 'Timesheet deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting timesheet: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete timesheet'], 500);
        }
    }
}
