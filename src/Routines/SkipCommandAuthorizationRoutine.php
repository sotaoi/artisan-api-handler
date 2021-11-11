<?php namespace Sotaoi\ArtisanApiHandler\Routines;

use Sotaoi\ArtisanApiHandler\Contracts\AuthorizationRoutineContract;

class SkipCommandAuthorizationRoutine extends AuthorizationRoutineContract {
  public function authorize(array $headers, string $command, array $payload, array $options): bool {
    return true;
  }
}
