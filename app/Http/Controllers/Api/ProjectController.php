<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $projects = $user->hasRole('admin')
            ? Project::with('tasks')->get()
            : Project::with('tasks')->where('user_id', $user->id)->get();

            return ApiResponse::success(data: $projects);

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function store(ProjectRequest $request)
    {
        try{

            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $project = Project::create($data);

            return ApiResponse::success(data: $project, code: 201);

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        try{

            $project->load('tasks.assignedUser');

            return ApiResponse::success(data: $project);

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function update(ProjectRequest $request, Project $project)
    {
        try{

            $project->update($request->validated());
            return ApiResponse::success(data: $project);

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }

    public function destroy(Project $project)
    {
        try{

            $project->delete();
            return ApiResponse::success(message: 'Proyecto eliminado');

        } catch (\Exception $e) {
            return ApiResponse::error('Ups, ha ocurrido un error inesperado', 500, $e->getMessage());
        }
    }
}
