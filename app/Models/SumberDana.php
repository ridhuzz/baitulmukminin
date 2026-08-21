<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SumberDana extends Model
{
    protected $table = 'sumber_dana';
    protected $guarded = [];

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiKeuangan::class, 'sumber_dana_id');
    }
}
