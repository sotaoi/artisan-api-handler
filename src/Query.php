<?php namespace Sotaoi\ArtisanApiHandler;

use Sotaoi\ArtisanApiHandler\Artifacts\ActionResult;

abstract class Query extends Contracts\ActionContract {
  protected $params = null;

  abstract public function handle(): ActionResult;

  public function __construct(array $params) {
    parent::__construct($params);
    $this->params = $this->getInput();
  }

  //

  protected function params(): \stdClass {
    return $this->params;
  }
}
