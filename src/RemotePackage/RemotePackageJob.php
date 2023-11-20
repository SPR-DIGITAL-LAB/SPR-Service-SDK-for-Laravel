<?php
namespace Spr\SprLaravelServiceSdk\RemotePackage;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spr\SprLaravelServiceSdk\RemotePackage\RemotePackage;


class RemotePackageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $action;
  public $data;
  public $package;
  public $callback;

    public function __construct($action, $data,$package,$callback)
    {
         $this->action=$action;
         $this->data=$data;
         $this->package=$package;
         $this->callback=$callback;

    }

    public function handle()
    {
        try {
            $rp = app(RemotePackage::class);
            $result = $rp->actionCall($this->action, $this->data); 
        } catch (\Exception $e) {
            Log::error("Error in RemotePackageJob: " . $e->getMessage());   
        }
        $this->release(10);
    }

    protected function performCallback($result)
    {
       
    }
}
