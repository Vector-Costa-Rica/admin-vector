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

class Client_image extends Model
{
    use HasFactory;



    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'client_images';

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
     * Get all client_images.
     *
     * @return Collection
     */
    /*    public static function getClient_images(): Collection
    {
        return Client_image::all();
    }*/

    /**
     * Get client_image by ID.
     *
     * @param int $id
     * @return Client_image|null
     */
    public function getClient_imageById(int $id): ?Client_image
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
