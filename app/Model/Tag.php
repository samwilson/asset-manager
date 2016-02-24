<?php

namespace App\Model;

class Tag extends \Illuminate\Database\Eloquent\Model
{

    protected $fillable = ['id', 'name'];

    public function assets()
    {
        return $this->belongsToMany('\\App\\Model\\Asset');
    }

    public function jobLists()
    {
        return $this->belongsToMany('\\App\\Model\\JobList');
    }

    /**
     * Merge a tag into this one, bringing with it all Assets and JobLists.
     *
     * @param \App\Model\Tag $otherTag
     */
    public function merge(Tag $otherTag)
    {
        if ($otherTag->id === $this->id) {
            return;
        }

        $params = ['t' => $this->id, 'ot' => $otherTag->id];
        \DB::update("UPDATE IGNORE asset_tag SET tag_id = :t WHERE tag_id = :ot", $params);
        \DB::delete("DELETE FROM asset_tag WHERE tag_id = :ot", ['ot' => $otherTag->id]);
        \DB::update("UPDATE IGNORE job_list_tag SET tag_id = :t WHERE tag_id = :ot", $params);
        \DB::delete("DELETE FROM job_list_tag WHERE tag_id = :ot", ['ot' => $otherTag->id]);

        $otherTag->delete();
    }

    /**
     * Get a list of Tag IDs from a CSV string.
     * @param string $tagList Comma-separated list of tags.
     * @return array
     */
    public static function getIds($tagList)
    {
        $out = array();
        foreach (explode(',', $tagList) as $t) {
            if (empty($t)) {
                continue;
            }
            $tag = self::firstOrCreate(['name' => trim($t)]);
            $out[$tag->id] = $tag->id;
        }
        return $out;
    }
}
