<?php

namespace App\Model;

class Tag extends \Illuminate\Database\Eloquent\Model {

    protected $fillable = ['id', 'name'];

    public function assets() {
        return $this->belongsToMany('\\App\\Model\\Asset');
    }

    public function jobLists() {
        return $this->belongsToMany('\\App\\Model\\JobList');
    }

    /**
     * Get a list of Tag IDs from a CSV string.
     * @param string $tagList Comma-separated list of tags.
     * @return array
     */
    public static function getIds($tagList) {
        $out = array();
        foreach (explode(',', $tagList) as $t) {
            $tag = self::firstOrCreate(['name' => $t]);
            $out[$tag->id] = $tag->id;
        }
        return $out;
    }

}
