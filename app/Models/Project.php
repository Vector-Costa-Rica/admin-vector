<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
    Builder
};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

/**
 * *
 * @mixin Builder
 */
class Project extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'client_id',
        'pricing',
        'repo',
        'url',
        'server',
        'state',
        'status',
        'condition'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = ['id'];

    /**
     * Get all projects.
     *
     * @return Collection
     */
    /*    public static function getProjects(): Collection
    {
        return Project::all();
    }*/

    /**
     * Get project by ID.
     *
     * @param int $id
     * @return Project|null
     */
    public function getProjectById(int $id): ?Project
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
        'client_id' => 'integer',
        'state' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'name' => null,
        'start_date' => null,
        'end_date' => null,
        'client_id' => null,
        'pricing' => null,
        'repo' => null,
        'url' => null,
        'server' => null,
        'state' => null,
        'status' => null,
        'condition' => null
    ];

    /**
     * Get the file attribute.
     */
    public function clientId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    /**
     * Get the state ID attribute.
     */
    public function stateId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }


    public function clients():  BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function project_state(): BelongsTo
    {
        return $this->belongsTo(Project_state::class, 'state', 'id');
    }

    public function client_doc(): HasMany
    {
        return $this->hasMany(Client_doc::class, 'project_id', 'id');
    }

    public function report(): HasMany
    {
        return $this->hasMany(Report::class, 'project_id', 'id');
    }

    public function branding(): HasMany
    {
        return $this->hasMany(Branding::class, 'project_id', 'id');
    }

    public function custom_asset(): HasMany
    {
        return $this->hasMany(Custom_asset::class, 'project_id', 'id');
    }

    public function asset(): HasMany
    {
        return $this->hasMany(Asset::class, 'project_id', 'id');
    }

    public function operational_doc(): HasMany
    {
        return $this->hasMany(Operational_doc::class, 'project_id', 'id');
    }

    public function client_image(): HasMany
    {
        return $this->hasMany(Client_image::class, 'project_id', 'id');
    }

    public function tech_doc(): HasMany
    {
        return $this->hasMany(Tech_doc::class, 'project_id', 'id');
    }

    public function proposal(): HasMany
    {
        return $this->hasMany(Proposal::class, 'project_id', 'id');
    }
}
