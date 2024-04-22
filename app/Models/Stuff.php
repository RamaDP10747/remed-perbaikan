<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use softDeletes;
    protected $fillable = ["name", "category"];

    public function stuffStock()
    {
        return $this->hasOne(StuffStock::class);
    }
    
    public function inboundStuffs()
    {
        return $this->hasMany(InboundStuffs::class);
    }

    public function lendings()
    {
        return $this->hasMany(Lendings::class);
    }
}
