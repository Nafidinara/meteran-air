<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Eloquent
 */

class Master extends Model
{
    use SoftDeletes;

    protected $table = 'masters';
    protected $primaryKey = 'master_id';

    protected $fillable = [
        'harga_m2_msy','harga_m2_brh','beban'
    ];

    protected $dates = ['deleted_at'];
}
