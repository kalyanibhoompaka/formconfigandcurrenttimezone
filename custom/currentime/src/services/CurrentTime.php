<?php

namespace Drupal\currentime\services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;

class CurrentTime {


    protected $configFactory;

    protected $time;

    protected $dateFormatter;

    public function __construct(ConfigFactoryInterface $configFactory,TimeInterface $time, DateFormatterInterface $dateFormatter){

$this->configFactory=$configFactory;

$this->time= $time;

$this->dateFormatter=$dateFormatter;

    }

    public function getCurrentTime(){

        $config=$this->configFactory->get('currentime.settings');
    $currentime_timezone=$congig->get('currentime_timezone')??'America:India';
    $now=$this->time->getCurrentTime();
    $current_date_time['current_date']=$this->dateFormatter->format($now, 'custom', 'l, d, F, Y', $currentime_timezone);
    $current_date_time['current_time']=$this->dateFormatter->format($now, 'custom', 'h : i a', $currentime_timezone);

    return $current_date_time;

    
    }
}