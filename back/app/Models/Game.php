<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'genre',
        'rating',
        'owner_id',
    ];

    public function maps(){
        return $this->hasMany( 'App\Models\Map', 'game_id', 'id' );
    }

    public function owner(){
        return $this->hasOne( 'App\Models\User', 'id', 'owner_id' );
    }
}
