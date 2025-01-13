<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,
    Collection,
    Builder,
    Relations\HasMany};

/**
 * *
 * @mixin Builder
 */
class Rate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rates';

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
     * Get all rates.
     *
     * @return Collection
     */
    /*    public static function getRates(): Collection
    {
        return Rate::all();
    }*/

    /**
     * Get rate by ID.
     *
     * @param int $id
     * @return Rate|null
     */
    public function getRateById(int $id): ?Rate
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

    public function service(): HasMany
    {
        return $this->hasMany(Service::class, 'rate_id', 'id');
    }
}
