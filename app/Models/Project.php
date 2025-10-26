<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'progress',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Actualiza automáticamente el progreso del proyecto
     * según las tareas completadas.
     */
    public function updateProgress()
    {
        $total = $this->tasks()->count();
        $completed = $this->tasks()->where('status', 'completed')->count();

        $this->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        $this->save();
    }
}
