<?php namespace Sotaoi\ArtisanApiHandler\Exceptions;

class ApiHandlerUidConflictException extends \Exception {
  public function __construct($message = 'API Handler UID already exists', $code = 0, ?\Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}
