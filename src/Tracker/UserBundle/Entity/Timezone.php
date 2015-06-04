<?php

namespace Tracker\UserBundle\Entity;

class Timezone
{
    const TIMEZONE_EUROPE_KIEV = 'Europe/Kiev';
    /**
     * @var array
     */
    static public $timezones = array(
        'Europe/Amsterdam' => 'Europe - Amsterdam',
        'Europe/Andorra' => 'Europe - Andorra',
        'Europe/Athens' => 'Europe - Athens',
        'Europe/Belfast' => 'Europe - Belfast',
        'Europe/Belgrade' => 'Europe - Belgrade',
        'Europe/Berlin' => 'Europe - Berlin',
        'Europe/Bratislava' => 'Europe - Amsterdam',
        'Europe/Kiev' => 'Europe - Kiev',
        'Europe/London' => 'Europe - London'
    );
}
