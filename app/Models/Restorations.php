<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restorations extends Model
{
    use SoftDeletes;
    protected $fillable = ["stuff_id", "lending_id", "date_time", "total_good_stuff", "total_defec_stuff"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    public function lendings()
    {
        return $this->belongsTo(Lendings::class);
    }
}
