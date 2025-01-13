<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * *
 * @mixin Builder
 */
class Client extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'picture',
        'id_city',
        'address',
        'zip',
        'phone',
        'mobile',
        'email',
        'web',
        'language_id'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $hidden = ['id'];

    /**
     * Get all clients.
     *
     * @return Collection
     */
    /*    public static function getClients(): Collection
    {
        return Client::all();
    }*/

    /**
     * Get client by ID.
     *
     * @param int $id
     * @return Client|null
     */
    public function getClientById(int $id): ?Client
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
        'id_city' => 'integer',
        'language_id' => 'integer',
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
        'picture' => null,
        'id_city' => null,
        'address' => null,
        'zip' => null,
        'phone' => null,
        'mobile' => null,
        'email' => null,
        'web' => null,
        'language_id' => null
    ];

    /**
     * Get the picture attribute.
     */
    public function picture(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    /**
     * Get the city ID attribute.
     */
    public function cityId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    /**
     * Get the language ID attribute.
     */
    public function languageId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value
        );
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'id_city', 'id');
    }

    public function state(): HasOneThrough
    {
        return $this->hasOneThrough(State::class, City::class, 'id', 'id', 'id_city', 'id_state');
    }

    public function country(): HasOneThrough
    {
        return $this->hasOneThrough(Country::class, State::class, 'id', 'id', 'id_state', 'id_country');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function project(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id', 'id');
    }
}
