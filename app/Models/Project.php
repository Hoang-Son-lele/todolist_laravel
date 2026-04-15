<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getDaysRemainingAttribute()
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getProgressPercentageAttribute()
    {

        $totalTasks = $this->tasks()->count();


        if ($totalTasks === 0) {
            return 0;
        }


        $sumPercentage = $this->tasks()->sum('completion_percentage');


        return (int) ceil($sumPercentage / $totalTasks);
    }
}
