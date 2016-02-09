<?php

namespace App\Model;

class Asset extends Taggable
{

    protected $fillable = ['id', 'identifier'];

    public function setIdentifierAttribute($value)
    {
        $this->attributes['identifier'] = substr($value, 0, 150);
    }

    public function suburb()
    {
        return $this->belongsTo('App\Model\Suburb');
    }

    public function state()
    {
        return $this->belongsTo('App\Model\State');
    }

    public function files()
    {
        return $this->belongsToMany('App\Model\File');
    }

    public function contacts()
    {
        return $this->belongsToMany('App\Model\Contact');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Model\Category');
    }

    public function jobs()
    {
        return $this->hasMany('App\Model\Job');
    }

    /**
     * Append a new comment to this Asset's comment field.
     * A blank line will be added between the old and new comments.
     * @param string $newComment The comment to append.
     */
    public function appendComments($newComment)
    {
        if (strpos($this->comments, $newComment) === false) {
            $this->comments = rtrim($this->comments, "\n") . "\n\n$newComment";
        }
    }
}
