<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPemasukkan extends Model
{
    use HasFactory;

    protected $table = 'tr_pemasukkan';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'kode',
        'tanggal_pemasukkan',
        'jam_pemasukkan',
        'balance_pemasukkan',
        'id_kategori_pemasukkan',
        'id_jenis_penyimpanan', // Tambahan kolom
        'catatan_pemasukkan',
        'deleted',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
    ];

    public function kategoriPemasukkan()
    {
        return $this->belongsTo(KategoriPemasukkan::class, 'id_kategori_pemasukkan');
    }

    public function jenisPenyimpanan()
    {
        return $this->belongsTo(JenisPenyimpanan::class, 'id_jenis_penyimpanan');
    }

    public function transaksi()
    {
        return $this->morphMany(Transaksi::class, 'dokumen');
    }
}
