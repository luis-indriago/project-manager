<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'assigned_to',
        'title',
        'description',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Evento para actualizar el progreso del proyecto al cambiar el estado
    protected static function booted()
    {
        static::saved(function ($task) {
            $task->project->updateProgress();
        });

        static::deleted(function ($task) {
            $task->project->updateProgress();
        });
    }
}
