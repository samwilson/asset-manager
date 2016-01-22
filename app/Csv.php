<?php

namespace App;

class Csv
{

    private $handle;
    private $head_name_map;
    private $head_index_map;
    private $current_line;

    public function __construct($filename)
    {
        $this->handle = fopen($filename, "r");
        if ($this->handle === false) {
            throw new \Exception("Unable to open $filename.");
        }
        // Find indexes of headers
        $header_row = fgetcsv($this->handle);
        $index = 0;
        foreach ($header_row as $header) {
            $this->head_name_map[$header] = $index;
            $this->head_index_map[$index] = $header;
            $index ++;
        }
    }

    public function get($col_name, $optional = false)
    {
        if (isset($this->head_name_map[$col_name])) {
            return trim($this->current_line[$col_name]);
        }
        if ($optional) {
            return false;
        } else {
            throw new \Exception("$col_name column not found." . print_r($this->head_name_map, true));
        }
    }

    public function next()
    {
        $line = fgetcsv($this->handle);
        $this->current_line = array();
        $index = 0;
        foreach ($this->head_index_map as $index => $header_name) {
            $this->current_line[$header_name] = (isset($line[$index])) ? $line[$index] : null;
        }
        return $line !== false;
    }

    /**
     * Whether or not this CSV has a header named $header.
     *
     * @param string $header
     * @return boolean
     */
    public function hasHeader($header)
    {
        return isset($this->head_name_map[$header]);
    }
}
