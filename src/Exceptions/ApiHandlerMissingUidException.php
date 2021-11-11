<?php namespace Sotaoi\ArtisanApiHandler\Exceptions;

class ApiHandlerMissingUidException extends \Exception {
  public function __construct($message = 'API Handler is missing the UID', $code = 0, ?\Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}
