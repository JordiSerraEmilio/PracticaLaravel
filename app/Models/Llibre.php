<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Autor;

class Llibre extends Model
{
    use HasFactory;

    public function formattedDate(){
        $date = date_create($this->dataP);
        return date_format($date, 'd/m/Y');
    }
    public function autor(){
        return $this->belongsTo(Autor::class);
    }
}
