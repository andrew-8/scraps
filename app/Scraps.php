<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scraps extends Model
{
    protected $fillable = ['title', 'text', 'publish', 'private', 'user_id', 'share_user_ids', 'created_at', 'updated_at'];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function setShareAttribute($key, $value) {
        return $this->attributes[$key] = json_encode($value);
    }

    public function getShareAttribute($key, $value) {
        return $this->attributes[$key] = json_decode($value);
    }

}
