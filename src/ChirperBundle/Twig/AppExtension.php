<?php

namespace ChirperBundle\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('calcTime', array($this, 'calculateTime')),
        );
    }

    public function calculateTime($date)
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }

        $currentDate = new DateTime('now');
        $sinceStartDate = $currentDate->diff($date);

        $minutes = $sinceStartDate->days * 24 * 60;
        $minutes += $sinceStartDate->h * 60;
        $minutes += $sinceStartDate->i;

        if ($minutes < 1) {
            return 'less than a minute';
        }

        if ($minutes < 60) {
            return $minutes . ' minute' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 60);

        if ($minutes < 24) {
            return $minutes . ' hour' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 24);

        if ($minutes < 30) {
            return $minutes . ' day' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 30);

        if ($minutes < 12) {
            return $minutes . ' month' . $this->pluralize($minutes);
        }

        $minutes = floor($minutes / 12);

        return $minutes . ' year' . $this->pluralize($minutes);
    }

    private function pluralize($value)
    {
        return $value != 1 ? 's' : '';
    }
}