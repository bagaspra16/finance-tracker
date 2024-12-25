<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'mm_transaksi';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_jenis_penyimpanan',
        'id_dokumen',
        'kode',
        'tanggal',
        'jam',
        'balance',
        'tipe',
        'transaksi',
        'deleted',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
    ];


    /**
     * Relationship to the jenis_penyimpanan table.
     */
    public function jenisPenyimpanan()
    {
        return $this->belongsTo(JenisPenyimpanan::class, 'id_jenis_penyimpanan');
    }

    /**
     * Polymorphic relationship to the dokumen (tr_pemasukkan or tr_pengeluaran).
     */
    public function dokumen()
    {
        return $this->morphTo();
    }
}
