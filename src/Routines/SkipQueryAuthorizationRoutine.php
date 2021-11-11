<?php namespace Sotaoi\ArtisanApiHandler\Routines;

use Sotaoi\ArtisanApiHandler\Contracts\AuthorizationRoutineContract;

class SkipQueryAuthorizationRoutine extends AuthorizationRoutineContract {
  public function authorize(array $headers, string $query, array $payload, array $options): bool {
    return true;
  }
}
