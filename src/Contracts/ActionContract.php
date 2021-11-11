<?php namespace Sotaoi\ArtisanApiHandler\Contracts;

use Sotaoi\ArtisanApiHandler\Artifacts\ActionResult;

abstract class ActionContract {
  private $input = null;

  abstract public function handle(): ActionResult;

  public function __construct(array $input) {
    $this->input = (object) $input;
  }

  protected function getInput(): \stdClass {
    return $this->input;
  }
}
