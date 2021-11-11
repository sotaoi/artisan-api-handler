<?php namespace Sotaoi\ArtisanApiHandler\Contracts;

abstract class ApiHandlerMiddlewareContract {
  abstract public function handle($request, \Closure $next);
}
