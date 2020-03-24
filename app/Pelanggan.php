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

    protected $primaryKey = 'pelanggan_id';

    protected $fillable = [
        'nama','level','telfon'
    ];

    protected $dates = ['deleted_at'];

    public function tagihan(){
        return $this->hasMany(Tagihan::class,'pelanggan_id');
    }

    public function rekening(){
        return $this->hasMany(Rekening::class,'pelanggan_id');
    }
}
