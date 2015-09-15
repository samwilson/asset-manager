<?php

namespace App\Model;

class CrewDate extends Model {

    public function crew() {
        return $this->belongsTo('App\Model\Crew');
    }

    /**
     * A crew is available on a particular date if that date lies outside of all the crew's dates of non-availability.
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

    public function setStartDateAttribute($value) {
        if ($value) {
            $this->attributes['start_date'] = $value;
        }
    }

    public function setEndDateAttribute($value) {
        if ($value) {
            $this->attributes['end_date'] = $value;
        }
    }

}
