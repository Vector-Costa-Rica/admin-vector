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

class State extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'states';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_country',
        'state_name'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $hidden = [];

    /**
     * Get all states.
     *
     * @return Collection
     */
    /*    public static function getStates(): Collection
    {
        return State::all();
    }*/

    /**
     * Get state by ID.
     *
     * @param int $id
     * @return State|null
     */
    public function getStateById(int $id): ?State
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
        'id_country' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'id_country' => null,
        'state_name' => null
    ];

    /**
     * Get the project ID attribute.
     */
    public function countryId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'id_country', 'id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'id_state', 'id');
    }

    public function getRouteKeyName()
    {
        return 'id'; // asegúrate de que esté usando 'id' y no otro campo
    }
}
