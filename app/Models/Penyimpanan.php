<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penyimpanan extends Model
{
    use HasFactory;

    protected $table = 'mm_penyimpanan';

    public $incrementing = false; 
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'balance',
        'id_jenis_penyimpanan',
        'id_dokumen',
        'deleted',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date',
    ];

    public function jenisPenyimpanan()
    {
        return $this->belongsTo(JenisPenyimpanan::class, 'id_jenis_penyimpanan');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

}
