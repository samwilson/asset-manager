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
        $headerRow = fgetcsv($this->handle);
        $index = 0;
        foreach ($headerRow as $h) {
            $header = snake_case($h);
            $this->head_name_map[$header] = $index;
            $this->head_index_map[$index] = $header;
            $index ++;
        }
    }

    public function get($colName, $optional = false)
    {
        $header = snake_case($colName);
        if (isset($this->head_name_map[$header])) {
            return trim($this->current_line[$header]);
        }
        if ($optional) {
            return false;
        } else {
            throw new \Exception("$colName column not found. Headers found:" . join(', ', $this->head_index_map));
        }
    }

    public function next()
    {
        $line = fgetcsv($this->handle);
        $this->current_line = array();
        $index = 0;
        foreach ($this->head_index_map as $index => $headerName) {
            $this->current_line[$headerName] = (isset($line[$index])) ? $line[$index] : null;
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
        return isset($this->head_name_map[snake_case($header)]);
    }
}
