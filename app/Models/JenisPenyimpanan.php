<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPenyimpanan extends Model
{
    use HasFactory;

    protected $table = 'mm_jenis_penyimpanan';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'keterangan',
        'deleted',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
    ];
}
