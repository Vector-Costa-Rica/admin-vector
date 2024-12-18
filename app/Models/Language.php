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

class Language extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'languages';

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
     * Get all languages.
     *
     * @return Collection
     */
    /*    public static function getLanguages(): Collection
    {
        return Language::all();
    }*/

    /**
     * Get language by ID.
     *
     * @param int $id
     * @return Language|null
     */
    public function getLanguageById(int $id): ?Language
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

    public function client(): HasMany
    {
        return $this->hasMany(Client::class, 'language_id', 'id');
    }
}
