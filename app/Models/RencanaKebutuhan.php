<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RencanaKebutuhan extends Model
{
    use HasFactory;

    protected $table = 'tr_rencana_kebutuhan';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'kode',
        'nama_kebutuhan',
        'balance_kebutuhan',
        'catatan_kebutuhan',
        'deleted',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
    ];
}
