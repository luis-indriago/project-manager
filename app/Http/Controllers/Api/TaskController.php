<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {   
        try{
            $query = Task::query()->with(['assignedUser', 'project']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            if ($request->filled('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            return ApiResponse::success(data: $query->get());

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }

        
    }

    public function store(TaskRequest $request)
    {
        try{
            $task = Task::create($request->validated());
            $task->project->updateProgress();

            return ApiResponse::success(data: $task, code: 201);

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function show(Task $task)
    {
        try{

            return ApiResponse::success(data: $task->load(['assignedUser', 'project']));

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function update(TaskRequest $request, Task $task)
    {
        try{

            $task->update($request->validated());
            $task->project->updateProgress();

            return ApiResponse::success(data: $task);

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function destroy(Task $task)
    {
        try{
            $project = $task->project;
            $task->delete();
            $project->updateProgress();

            return ApiResponse::success(message: 'Tarea eliminada');

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
{
    $task->update($request->validated());
    $task->project->updateProgress();   // si usas este helper

    return ApiResponse::success(data: $task);   // o TaskResource::make($task)
}
}
