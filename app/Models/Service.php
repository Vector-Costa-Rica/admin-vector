<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{
    Model,
    Collection,
    Builder,
    Relations\BelongsTo,
    Casts\Attribute,
    Factories\HasFactory
};

/**
 * *
 * @mixin Builder
 */
class Service extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product',
        'code',
        'description',
        'price',
        'rate_id'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = ['id'];

    /**
     * Get all services.
     *
     * @return Collection
     */
    /*    public static function getServices(): Collection
    {
        return Service::all();
    }*/

    /**
     * Get service by ID.
     *
     * @param int $id
     * @return Service|null
     */
    public function getServiceById(int $id): ?Service
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
        'rate_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'product' => null,
        'code' => null,
        'description' => null,
        'price' => null,
        'rate_id' => null
    ];

    /**
     * Get the rate ID attribute.
     */
    public function rateId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class, 'rate_id', 'id');
    }
}
