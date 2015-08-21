<?php

namespace App\Model;

abstract class Taggable extends Model {

    public function tags() {
        return $this->belongsToMany('App\Model\Tag');
    }

    public function tagsAsString() {
        $tags = array();
        foreach ($this->tags()->orderBy('name', 'ASC')->get() as $tag) {
            $tags[] = $tag->name;
        }
        return join(', ', $tags);
    }

}
