<?php
namespace App\Frontend;

use \OCFram\Application;

class FrontendApplication extends Application
{
  public function __construct()
  {
    parent::__construct();

    $this->name = 'Frontend';
    $this->controller = $this->getController();
  }

  public function run()
  {
    $this->controller->execute();

    $this->httpResponse->setPage($this->controller->page());
    $this->httpResponse->send();
  }
  
  public function controller()
  {
  	return $this->controller;
  }
}