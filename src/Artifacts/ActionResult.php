<?php namespace Sotaoi\ArtisanApiHandler\Artifacts;

use Illuminate\Http\JsonResponse;

class ActionResult {
  public $statusCode = 400;
  public $success = false;
  public $title = null;
  public $message = null;
  public $output = null;
  public $validations = null;

  private $_override = null;

  public function __construct(
    int $statusCode,
    ?string $title,
    ?string $message,
    ?object $output,
    ?object $validations,
    ?object $override = null
  ) {
    $this->statusCode = $statusCode;
    $this->success = $statusCode >= 200 && $statusCode < 300;
    $this->title = $title;
    $this->message = $message;
    $this->output = $output;
    $this->validations = $validations;

    $this->_override = $override;
  }

  public function toJson(): JsonResponse {
    return response()->json($this->getResult(), $this->statusCode);
  }

  public function getResult(): object {
    if ($this->isOverridden()) {
      return $this->_override;
    }
    return (object) [
      'statusCode' => $this->statusCode,
      'success' => $this->success,
      'title' => $this->title,
      'message' => $this->message,
      'output' => $this->output,
      'validations' => $this->validations,
    ];
  }

  public function isOverridden(): bool {
    return $this->_override !== null;
  }

  public static function override(object $result, int $statusCode = 200): ActionResult {
    return new ActionResult($statusCode, null, null, null, null, $result);
  }
}
