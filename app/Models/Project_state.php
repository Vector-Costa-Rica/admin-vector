<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
    Builder,
    Relations\HasMany
};
/**
 * *
 * @mixin Builder
 */
class Project_state extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_states';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = ['id'];

    /**
     * Get all project states.
     *
     * @return Collection
     */
    /*    public static function getProject_states(): Collection
    {
        return Project_state::all();
    }*/

    /**
     * Get project state by ID.
     *
     * @param int $id
     * @return Project_state|null
     */
    public function getProject_stateById(int $id): ?Project_state
    {
        return self::find($id);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'name' => null
    ];

    public function project(): HasMany
    {
        return $this->hasMany(Project::class, 'state', 'id');
    }
}
