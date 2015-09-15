<?php

namespace App\Model;

abstract class DateRange extends Model {

    protected $nullable = ['start_date', 'end_date'];

    /**
     * An item is available on a particular date if that date lies outside of all the item's dates of non-availability.
     * @param \DateTime|string $date
     * @return boolean
     */
    public function availableOn($date) {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        $start = new \DateTime($this->start_date);
        $end = new \DateTime($this->end_date);

        // Open start date.
        if (is_null($this->start_date) && !is_null($this->end_date)) {
            return $date > $end;
        }

        // Open end date.
        if (!is_null($this->start_date) && is_null($this->end_date)) {
            return $date < $start;
        }

        // Start and End specified.
        if (!is_null($this->start_date) && !is_null($this->end_date)) {
            return ($date < $start) || ($date > $end);
        }

        // Shouldn't ever be here! :-)
        throw new \Exception('Something wrong with the dates.');
    }

}
