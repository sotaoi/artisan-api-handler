<?php namespace Sotaoi\ArtisanApiHandler;

use Sotaoi\ArtisanApiHandler\Artifacts\ActionResult;

class ApiHandler extends Contracts\ApiHandlerContract {
  public function __construct(string $uid, ApiHandlerConfig $config) {
    parent::__construct($uid, $config);
    $this->config = $config;
  }

  public function handleCommand(array $headers, string $command, array $payload, array $options): ActionResult {
    try {
      $headers['authorization'] = $headers['authorization'] ?? ($options['authorization'] ?? null);
      $commandHandler = $this->conf()->command($command, $payload);
      if (!$commandHandler) {
        return $this->handleMissingCommand($command, $payload);
      }
      $authorizationRoutine = $this->getCommandAuthorizationRoutine();
      if (!$authorizationRoutine->authorize($headers, $command, $payload, $options)) {
        return $this->handleDenyCommand($command, $payload);
      }
      return $commandHandler->handle();
    } catch (\Exception $ex) {
      return $this->handleException($ex);
    } catch (\Error $err) {
      return $this->handleError($err);
    }
  }

  public function handleQuery(array $headers, string $query, array $params, array $options): ActionResult {
    try {
      $headers['authorization'] = $headers['authorization'] ?? ($options['authorization'] ?? null);
      $queryHandler = $this->conf()->query($query, $params);
      if (!$queryHandler) {
        return $this->handleMissingQuery($query, $params);
      }
      $authorizationRoutine = $this->getQueryAuthorizationRoutine();
      if (!$authorizationRoutine->authorize($headers, $query, $params, $options)) {
        return $this->handleDenyQuery($query, $params);
      }
      return $queryHandler->handle();
    } catch (\Exception $ex) {
      return $this->handleException($ex);
    } catch (\Error $err) {
      return $this->handleError($err);
    }
  }

  public function handleStatusQuery($params = []): ActionResult {
    try {
      return $this->conf()
        ->statusQuery($params)
        ->handle();
    } catch (\Exception $ex) {
      return $this->handleException($ex);
    } catch (\Error $err) {
      return $this->handleError($err);
    }
  }

  public function handleException(\Exception $ex): ActionResult {
    \Log::error($ex);
    return new ActionResult(400, '#' . $ex->getCode(), $ex->getMessage(), null, null, null);
  }

  public function handleError(\Error $err): ActionResult {
    \Log::error($err);
    return new ActionResult(400, '#' . $err->getCode(), $err->getMessage(), null, null, null);
  }

  public function handleDenyCommand(string $command, array $payload): ActionResult {
    return new ActionResult(401, 'Unauthorized', 'Access Denied', null, null, null);
  }

  public function handleDenyQuery(string $query, array $params): ActionResult {
    return new ActionResult(401, 'Unauthorized', 'Access Denied', null, null, null);
  }

  public function handleDenyStatusQuery(array $input): ActionResult {
    return new ActionResult(401, 'Unauthorized', 'Access Denied', null, null, null);
  }

  public function handleMissingCommand(string $command, array $input): ActionResult {
    return new ActionResult(404, 'No Command', 'Requested command does not exist', null, null, null);
  }

  public function handleMissingQuery(string $query, array $input): ActionResult {
    return new ActionResult(404, 'No Query', 'Requested query does not exist', null, null, null);
  }
}
