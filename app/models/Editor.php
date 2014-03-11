<?php

class Editor extends Eloquent {
 
    protected $table = 'editor';

    public function user() {
        return $this->belongsTo('User');
    }

    public function comments() {
        return $this->morphMany('Comment', 'commentable');
    }

}