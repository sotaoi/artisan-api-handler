<?php namespace Sotaoi\ArtisanApiHandler\Contracts;

abstract class AuthorizationRoutineContract {
  abstract public function authorize(array $headers, string $action, array $input, array $options): bool;

  public function authorizeStatusQuery(array $headers, array $input, array $options): bool {
    return true;
  }
}
