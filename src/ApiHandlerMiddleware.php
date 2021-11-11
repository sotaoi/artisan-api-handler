<?php namespace Sotaoi\ArtisanApiHandler;

use Sotaoi\ArtisanApiHandler\Contracts\ApiHandlerContract;
use Illuminate\Support\Facades\Request;

class ApiHandlerMiddleware extends Contracts\ApiHandlerMiddlewareContract {
  public function handle($request, \Closure $next) {
    $options = [];
    foreach (isset($_GET) && is_array($_GET) ? $_GET : [] as $key => $value) {
      $options[mb_strtolower($key)] = $value;
    }

    /** @var Request $request */
    $input = mb_strtolower($request->method()) === 'post' ? $request->post() : [];
    foreach (ApiHandlerContract::conjureHandlers() as $handler) {
      /** @var ApiHandlerContract $handler */
      $uris = $handler->getUris();
      foreach ($uris->statusQueryEndpoints as $statusQueryEndpoint) {
        $statusQueryEndpoint[0] !== '/' && ($statusQueryEndpoint = '/' . $statusQueryEndpoint);
        if ($request->getRequestUri() === $statusQueryEndpoint) {
          $authorizationRoutine = $handler->getQueryAuthorizationRoutine();
          if (!$authorizationRoutine->authorizeStatusQuery($request->header(), $input, $options)) {
            return $handler->handleDenyStatusQuery($input);
          }
          return $handler->handleStatusQuery()->toJson();
        }
      }

      if ($uris->commandsEndpointPrefix === mb_substr($request->getRequestUri(), 0, mb_strlen($uris->commandsEndpointPrefix))) {
        $command = mb_substr($request->getRequestUri(), mb_strlen($uris->commandsEndpointPrefix));
        $command && ($command = explode('?', $command)[0]);
        $command && $command[0] === '/' && ($command = mb_substr($command, 1));
        return $handler->handleCommand($request->header(), $command, $input, $options)->toJson();
      }

      if ($uris->queriesEndpointPrefix === mb_substr($request->getRequestUri(), 0, mb_strlen($uris->queriesEndpointPrefix))) {
        $query = mb_substr($request->getRequestUri(), mb_strlen($uris->queriesEndpointPrefix));
        $query && ($query = explode('?', $query)[0]);
        $query && $query[0] === '/' && ($query = mb_substr($query, 1));
        return $handler->handleQuery($request->header(), $query, $input, $options)->toJson();
      }
    }

    return $next($request);
  }
}
