<?php namespace Sotaoi\ArtisanApiHandler\Exceptions;

class ApiHandlerConfigException extends \Exception {
  public function __construct(array $errorMessages) {
    if (!count($errorMessages)) {
      parent::__construct('Error, bad SOTAOI Api Handler config');
      return;
    }
    $errorMsg = 'Error, bad SOTAOI Api Handler config: ' . implode('; ', $errorMessages);
    parent::__construct($errorMsg);
  }
}
