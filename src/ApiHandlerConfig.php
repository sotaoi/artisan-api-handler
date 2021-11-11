<?php namespace Sotaoi\ArtisanApiHandler;

use Sotaoi\ArtisanApiHandler\Command;
use Sotaoi\ArtisanApiHandler\Contracts\AuthorizationRoutineContract;
use Sotaoi\ArtisanApiHandler\Query;
use ReflectionClass;

class ApiHandlerConfig {
  public $statusQuery = null;
  public $commandsEndpointPrefix = null;
  public $queriesEndpointPrefix = null;
  public $commandAuthorizationRoutine = null;
  public $queryAuthorizationRoutine = null;

  protected $commands = null;
  protected $queries = null;

  public function __construct(object $options) {
    $validationErrors = [];
    $validationPassed = true;
    $abstractCommandReflection = new ReflectionClass(Command::class);
    $abstractQueryReflection = new ReflectionClass(Query::class);

    isset($options->commandsEndpointPrefix) &&
      is_string($options->commandsEndpointPrefix) &&
      $options->commandsEndpointPrefix[0] !== '/' &&
      ($options->commandsEndpointPrefix = '/' . $options->commandsEndpointPrefix);

    if (
      !isset($options->statusQuery) ||
      !is_string($options->statusQuery) ||
      !class_exists($options->statusQuery) ||
      !(
        (new ReflectionClass($options->statusQuery))->isInstance($abstractQueryReflection) ||
        (new ReflectionClass($options->statusQuery))->isSubclassOf($abstractQueryReflection)
      )
    ) {
      $validationErrors[] = 'Status Query is missing or invalid';
      $validationPassed = false;
    }

    $statusQueryEndpointsOk = true;
    if (
      !isset($options->statusQueryEndpoints) ||
      !is_array($options->statusQueryEndpoints) ||
      !count($options->statusQueryEndpoints)
    ) {
      $statusQueryEndpointsOk = false;
    }
    foreach ($options->statusQueryEndpoints ?? [] as $statusQueryEndpoint) {
      if (!isset($statusQueryEndpoint) || !is_string($statusQueryEndpoint) || !$statusQueryEndpoint) {
        $statusQueryEndpointsOk = false;
      }
    }
    if (!$statusQueryEndpointsOk) {
      $validationErrors[] = 'Status query endpoint(s) is/are missing or invalid';
      $validationPassed = false;
    }

    if (
      !isset($options->commandsEndpointPrefix) ||
      !is_string($options->commandsEndpointPrefix) ||
      mb_strlen($options->commandsEndpointPrefix) < 2
    ) {
      $validationErrors[] = 'Commands endpoint prefix is missing or invalid';
      $validationPassed = false;
    }

    if (
      !isset($options->queriesEndpointPrefix) ||
      !is_string($options->queriesEndpointPrefix) ||
      mb_strlen($options->queriesEndpointPrefix) < 2
    ) {
      $validationErrors[] = 'Queries endpoint prefix is missing or invalid';
      $validationPassed = false;
    }

    if (
      !isset($options->commandAuthorizationRoutine) ||
      !($options->commandAuthorizationRoutine instanceof Contracts\AuthorizationRoutineContract)
    ) {
      $validationErrors[] = 'Invalid authorization handler';
      $validationPassed = false;
    }

    if (
      !isset($options->queryAuthorizationRoutine) ||
      !($options->queryAuthorizationRoutine instanceof Contracts\AuthorizationRoutineContract)
    ) {
      $validationErrors[] = 'Invalid authorization handler';
      $validationPassed = false;
    }

    if (!isset($options->commands) || !is_array($options->commands)) {
      $validationErrors[] = 'Invalid commands';
      $validationPassed = false;
    }
    foreach ($options->commands ?? [] as $commandIndex => $command) {
      if (
        !isset($command) ||
        !is_string($command) ||
        !class_exists($command) ||
        !(
          (new ReflectionClass($command))->isInstance($abstractCommandReflection) ||
          (new ReflectionClass($command))->isSubclassOf($abstractCommandReflection)
        )
      ) {
        $validationErrors[] = 'Command "' . $commandIndex . '" is not a Sotaoi\ArtisanApiHandler\Command instance / child';
        $validationPassed = false;
      }
    }

    if (!isset($options->queries) || !is_array($options->queries)) {
      $validationErrors[] = 'Invalid queries';
      $validationPassed = false;
    }
    foreach ($options->queries ?? [] as $queryIndex => $query) {
      if (
        !isset($query) ||
        !is_string($query) ||
        !class_exists($query) ||
        !(
          (new ReflectionClass($query))->isInstance($abstractQueryReflection) ||
          (new ReflectionClass($query))->isSubclassOf($abstractQueryReflection)
        )
      ) {
        $validationErrors[] = 'Query "' . $queryIndex . '" is not a Sotaoi\ArtisanApiHandler\Query instance / child';
        $validationPassed = false;
      }
    }

    if (!$validationPassed) {
      throw new Exceptions\ApiHandlerConfigException($validationErrors);
    }

    $this->statusQuery = $options->statusQuery;
    $this->statusQueryEndpoints = $options->statusQueryEndpoints;
    $this->commandsEndpointPrefix = $options->commandsEndpointPrefix;
    $this->queriesEndpointPrefix = $options->queriesEndpointPrefix;
    $this->commandAuthorizationRoutine = $options->commandAuthorizationRoutine;
    $this->queryAuthorizationRoutine = $options->queryAuthorizationRoutine;

    $this->commands = (object) $options->commands;
    $this->queries = (object) $options->queries;
  }

  public function command(string $command, array $payload): ?Command {
    return isset($this->commands->{$command}) ? new $this->commands->{$command}($payload) : null;
  }

  public function query(string $query, array $params): ?Query {
    return isset($this->queries->{$query}) ? new $this->queries->{$query}($params) : null;
  }

  public function statusQuery(array $params = []): ?Query {
    $statusQuery = $this->statusQuery;
    return new $statusQuery($params);
  }

  public function getCommandAuthorizationRoutine(): AuthorizationRoutineContract {
    return $this->commandAuthorizationRoutine;
  }

  public function getQueryAuthorizationRoutine(): AuthorizationRoutineContract {
    return $this->queryAuthorizationRoutine;
  }

  public function getUris(): object {
    return (object) [
      'statusQueryEndpoints' => $this->statusQueryEndpoints,
      'commandsEndpointPrefix' => $this->commandsEndpointPrefix,
      'queriesEndpointPrefix' => $this->queriesEndpointPrefix,
    ];
  }
}
