<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'tr_pengeluaran';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'kode',
        'tanggal_pengeluaran',
        'jam_pengeluaran',
        'balance_pengeluaran',
        'id_kategori_pengeluaran',
        'id_jenis_penyimpanan', 
        'catatan_pengeluaran',
        'id_rencana_kebutuhan',
        'deleted',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
    ];

    public function kategoriPengeluaran()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori_pengeluaran');
    }

    public function jenisPenyimpanan()
    {
        return $this->belongsTo(JenisPenyimpanan::class, 'id_jenis_penyimpanan');
    }

    public function rencanaKebutuhan()
    {
        return $this->belongsTo(RencanaKebutuhan::class, 'id_rencana_kebutuhan');
    }

    public function transaksi()
    {
        return $this->morphMany(Transaksi::class, 'dokumen');
    }
}
