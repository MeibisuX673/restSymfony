<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\EventDispatcher\Event;


class MyEvent extends Event
{
    const NAME = 'my.event';



}