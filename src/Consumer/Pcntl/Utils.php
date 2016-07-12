<?php

namespace Tnc\Service\EventDispatcher\Consumer\Pcntl;

class Utils
{
    public static function setProcessTitle($title)
    {
        /*if(function_exists('cli_set_process_title')) {
            cli_set_process_title($title); //PHP >= 5.5.
        } else if(function_exists('setproctitle')) {
            setproctitle($title); //PECL proctitle
        }*/
    }
}