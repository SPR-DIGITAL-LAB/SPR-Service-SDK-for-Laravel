<?php

namespace Spr\SprLaravelServiceSdk\RemotePackage;

use App\Exceptions\Errors;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Validator;
use Spr\SprLaravelServiceSdk\Support\Controller;

class RemoteApi extends Controller
{
  public $actionExecutors = array();

  public function action($message)
  {
    
    return $message;

  }

  public function actionFailed($reason, $trace = [])
  {
    return $this->action([
      'action' => false,
      'reason' => $reason,
      'trace' => $trace
    ]);
  }

  public function actionFailedException($exception)
  {
    return $this->actionFailed("SYSTEM_RUNTIME_EXCEPTION", [
      'error' => $exception->getMessage(),
      'file' => $exception->getFile(),
      'line' => $exception->getLine(),
    ]);
  }

  /**
   * @return array
   */
  public function actionOK($message)
  {
    $message['action'] = true;
    return $this->action($message);
  }

  public function try($block, $error = null)
  {
    try {
      return $block();
    } catch (Throwable $t) {
      if ($error == null) {
        return $this->actionFailedException($t);
      } else return $error($t);
    }
  }
}
   