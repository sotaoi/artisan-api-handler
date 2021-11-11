<?php namespace Sotaoi\ArtisanApiHandler;

use Sotaoi\ArtisanApiHandler\Artifacts\ActionResult;

abstract class Command extends Contracts\ActionContract {
  protected $payload = null;

  abstract public function handle(): ActionResult;

  public function __construct(array $payload) {
    parent::__construct($payload);
    $this->payload = $this->getInput();
  }

  //

  protected function payload(): object {
    return $this->payload;
  }
}
