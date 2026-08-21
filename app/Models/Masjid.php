<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Masjid extends Model
{
    protected $table = 'masjid';
    protected $guarded = [];

    public function strukturOrganisasi(): HasMany
    {
        return $this->hasMany(StrukturOrganisasi::class);
    }
}
