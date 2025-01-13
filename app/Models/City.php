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

class City extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_state',
        'city'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = [];

    /**
     * Get all cities.
     *
     * @return Collection
     */
    /*    public static function getCities(): Collection
    {
        return City::all();
    }*/

    /**
     * Get City by ID.
     *
     * @param int $id
     * @return City|null
     */
    public function getCityById(int $id): ?City
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
        'id_state' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'id_state' => null,
        'city' => null
    ];

    /**
     * Get the project ID attribute.
     */
    public function stateId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'id_state', 'id');
    }

    public function client(): HasMany
    {
        return $this->hasMany(Client::class, 'id_city', 'id');
    }
}
