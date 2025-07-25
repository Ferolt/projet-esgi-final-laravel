<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'project_id',
        'couleur',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'colonne_id');
    }
}
