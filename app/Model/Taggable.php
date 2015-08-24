<?php

namespace App\Model;

abstract class Taggable extends Model {

    public function tags() {
        return $this->belongsToMany('App\Model\Tag');
    }

    /**
     * Get things tagged with all of a given comma-separated list of tags.
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $tagged
     */
    public function scopeTagged($query, $tagged) {
        foreach (explode(',', $tagged) as $tag) {
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }
        return $query;
    }

    public function tagsAsString() {
        $tags = array();
        foreach ($this->tags()->orderBy('name', 'ASC')->get() as $tag) {
            $tags[] = $tag->name;
        }
        return join(', ', $tags);
    }

}
