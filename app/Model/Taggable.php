<?php

namespace App\Model;

abstract class Taggable extends Model {

    public function tags() {
        return $this->belongsToMany('App\Model\Tag');
    }

    /**
     * Add tags to this item from either an array of Tag IDs or a comma-separated string.
     * @uses \App\Model\Tag::getIds()
     * @param string|int[] $tags
     */
    public function addTags($tags) {
        $tagIds = (is_string($tags)) ? Tag::getIds($tags) : $tags;
        $data = [];
        $sql = 'INSERT IGNORE INTO asset_tag (asset_id,tag_id) VALUES';
        for ($i=0; $i<count($tagIds); $i++) {
            $tagId = array_values($tagIds)[$i];
            $data[":asset_$i"] = $this->id;
            $data[":tag_$i"] = $tagId;
            $sql .= " (:asset_$i,:tag_$i), ";
        }
        $sql = rtrim($sql, ' ,');
        \DB::insert($sql, $data);
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
