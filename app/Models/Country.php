<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
    Builder
};
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * *
 * @mixin Builder
 */
class Country extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'iso',
        'country_name'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = ['id'];

    /**
     * Get all countries.
     *
     * @return Collection
     */
     /*   public static function getCountries(): Collection
    {
        return Country::all();
    }*/

    /**
     * Get asset by ID.
     *
     * @param int $id
     * @return Country|null
     */
    public function getCountryById(int $id): ?Country
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
        'iso' => null,
        'country_name' => null
    ];

    public function state(): HasMany{
        return $this->hasMany(State::class, 'id_country', 'id');
    }
}
