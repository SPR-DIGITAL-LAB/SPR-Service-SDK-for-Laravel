<?php

namespace Spr\SprLaravelServiceSdk\RemotePackage;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\Dispatchable;


class RemotePackage extends RemoteApi
{
    protected $actionProps = [];
    
    public function __invoke()
    {
        $this->mount();
        $action = request()->post("_action");
        $data = request()->post("_data");
        $async = request()->post("_async", false);
        $callback = request()->post("_callback");

        if ($action) {
            if ($async) {

                $this->dispatch(new RemotePackageJob(RemotePackage::class, $action, $data, $callback));
                return $this->actionOK(['message' => 'Job dispatched successfully']);
            } else {
                return $this->actionCall($action, $data);
            }
        }
    }
    public function actionCall($action, $data)
    {
        $callable = [$this, $action];
        return $callable($data);
    }

    public function mount()
    {
    
    }

    public function defineActions($action, $description, $props = [])
    {
        $props['description'] = $description;
        $this->actionProps[$action] = $props;
    }
}
