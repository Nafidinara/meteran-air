<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Eloquent
 */

class Pelanggan extends Model
{
    use softDeletes;

    protected $table = 'pelanggans';

    protected $primaryKey = "pelanggan_id";

    protected $fillable = [
        'nama','level','telfon'
    ];

    public function tagihan(){
        return $this->hasMany(Tagihan::class,'pelanggan_id');
    }
}
