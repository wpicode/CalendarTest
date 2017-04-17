<?php

namespace Calendar;

use DateTime;
use DateTimeInterface;

class Calendar implements CalendarInterface
{
    protected $dateTime;

    public function __construct(DateTimeInterface $datetime)
    {
        $this->dateTime = $datetime;
    }

    public function getDay()
    {
        return (int)$this->dateTime->format('j');
    }

    public function getWeekDay()
    {
        return (int)$this->dateTime->format('N');
    }

    public function getFirstWeekDay()
    {
        return (int)$this->firstDayOfMonth($this->dateTime)->format('N');
    }

    public function getFirstWeek()
    {
        return (int)$this->firstDayOfMonth($this->dateTime)->format('W');
    }

    public function getNumberOfDaysInThisMonth()
    {
        return (int)$this->dateTime->format('t');
    }

    public function getNumberOfDaysInPreviousMonth()
    {
        return (int)$this->firstDayOfMonth($this->dateTime)
            ->modify('-1 month')
            ->format('t');
    }

    public function getCalendar()
    {
        $outputCalendar = [];
        $startDate = $this->getCalendarStartDate($this->dateTime);
        $endDate = $this->getCalendarEndDate($this->dateTime);

        $currentDate = clone $startDate;
        for ($displayWeek = 0; $displayWeek < 6; $displayWeek++) {
            if ($currentDate <= $endDate) {
                $currentWeekNum = (int)$currentDate->format('W');
                for ($currentDay = 1; $currentDay <= 7; $currentDay++) {
                    $dayOfCurrentMonth = $currentDate->format('j');
                    $outputCalendar[$currentWeekNum][$dayOfCurrentMonth] = $this->shouldHighlightWeek($currentWeekNum);
                    $currentDate->modify('+1 day');
                }
            }
        }

        return $outputCalendar;
    }

    /**
     * @param int $currentWeekNum
     * @return bool
     */
    public function shouldHighlightWeek($currentWeekNum)
    {
        $newDateTime = $this->currentDateTime($this->dateTime);
        return ($currentWeekNum == $newDateTime->modify('-7 days')->format('W'));
    }

    /**
     * @param DateTimeInterface $datetime
     * @return DateTime
     */
    private function currentDateTime(DateTimeInterface $datetime)
    {
        return $this->dateTimeFactory($datetime, 'd-m-Y');
    }

    /**
     * @param DateTimeInterface $datetime
     * @return DateTime
     */
    private function firstDayOfMonth(DateTimeInterface $datetime)
    {
        return $this->dateTimeFactory($datetime, '1-m-Y');
    }

    /**
     * @param DateTimeInterface $datetime
     * @return DateTime
     */
    private function lastDayOfMonth(DateTimeInterface $datetime)
    {
        return $this->dateTimeFactory($datetime, 't-m-Y');
    }

    /**
     * @param DateTimeInterface $datetime
     * @param string $format
     * @return DateTime
     */
    private function dateTimeFactory(DateTimeInterface $datetime, $format)
    {
        return new DateTime($datetime->format($format));
    }

    /**
     * @param $dateTime
     * @return DateTime
     */
    private function getCalendarStartDate($dateTime)
    {
        $startDate = $this->firstDayOfMonth($dateTime);
        $startDate->setISODate($startDate->format('o'), $startDate->format('W'));
        return $startDate;
    }

    /**
     * @param $dateTime
     * @return DateTime
     */
    private function getCalendarEndDate($dateTime)
    {
        $endDate = $this->lastDayOfMonth($dateTime);
        $endDate->setISODate($endDate->format('o'), $endDate->format('W'));
        return $endDate;
    }

}