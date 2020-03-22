<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Eloquent
 */

class Tagihan extends Model
{
    use softDeletes;

    protected $primaryKey = "tagihan_id";
    protected $table = 'tagihans';

    protected $fillable = [
        'pelanggan_id','mtr_awal','mtr_akhir','mtr_jumlah','harga_m2','jml_m2',
        'beban','hutang','simpanan','simp_status','total_tagihan'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
