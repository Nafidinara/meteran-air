<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Eloquent
 */

class Rekening extends Model
{
    use SoftDeletes;

    protected $table = 'rekenings';
    protected $primaryKey = 'rekening_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'pelanggan_id','hutang','simpanan','total_pembayaran'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
