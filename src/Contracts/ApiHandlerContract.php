<?php namespace Sotaoi\ArtisanApiHandler\Contracts;

use Sotaoi\ArtisanApiHandler\ApiHandlerConfig;
use Sotaoi\ArtisanApiHandler\Artifacts\ActionResult;
use Sotaoi\ArtisanApiHandler\Exceptions\ApiHandlerMissingUidException;
use Sotaoi\ArtisanApiHandler\Exceptions\ApiHandlerUidConflictException;

abstract class ApiHandlerContract {
  protected $uid = null;
  protected $config = null;

  protected static $uidList = [];

  protected static $handlers = [];

  abstract public function handleCommand(array $headers, string $command, array $payload, array $options): ActionResult;
  abstract public function handleQuery(array $headers, string $query, array $params, array $options): ActionResult;
  abstract public function handleStatusQuery($params = []): ActionResult;
  abstract public function handleException(\Exception $ex): ActionResult;
  abstract public function handleError(\Error $ex): ActionResult;
  abstract public function handleDenyCommand(string $command, array $payload): ActionResult;
  abstract public function handleDenyQuery(string $query, array $params): ActionResult;
  abstract public function handleDenyStatusQuery(array $input): ActionResult;
  abstract public function handleMissingCommand(string $command, array $input): ActionResult;
  abstract public function handleMissingQuery(string $query, array $input): ActionResult;

  public function __construct(string $uid, ApiHandlerConfig $config) {
    if (!$uid) {
      throw new ApiHandlerMissingUidException();
    }
    if (in_array($uid, static::$uidList)) {
      throw new ApiHandlerUidConflictException();
    }

    $this->uid = $uid;
    $this->config = $config;
    static::$uidList[] = $uid;

    static::$handlers[$uid] = $this;
  }

  public function getCommandAuthorizationRoutine(): AuthorizationRoutineContract {
    return $this->conf()->commandAuthorizationRoutine;
  }

  public function getQueryAuthorizationRoutine(): AuthorizationRoutineContract {
    return $this->conf()->queryAuthorizationRoutine;
  }

  public function getUris(): object {
    return $this->conf()->getUris();
  }

  //

  public static function conjureHandlers(): object {
    return (object) static::$handlers;
  }

  //

  protected function conf(): ApiHandlerConfig {
    return $this->config;
  }
}
