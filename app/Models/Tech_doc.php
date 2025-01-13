<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
    Builder
};
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * *
 * @mixin Builder
 */

class Tech_doc extends Model
{
    use HasFactory;



    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tech_docs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'project_id',
        'file'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = ['id'];

    /**
     * Get all tech_docs.
     *
     * @return Collection
     */
    /*    public static function getTech_docs(): Collection
    {
        return Tech_doc::all();
    }*/

    /**
     * Get tech_doc by ID.
     *
     * @param int $id
     * @return Tech_doc|null
     */
    public function getTech_docById(int $id): ?Tech_doc
    {
        return self::find($id);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'project_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'project_id' => null,
        'file' => null
    ];

    /**
     * Get the file attribute.
     */
    public function file(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    /**
     * Get the project ID attribute.
     */
    public function projectId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
