<?php
namespace App\Repositories\Log;

use Illuminate\Support\ServiceProvider;
use App\Entities\CAKE\AccessLog;
use App\Repositories\Log\AccessLogRepository;


class AccessLogRepositoryServiceProvider extends ServiceProvider
{

	public function register()
	{
		$this->app->bind('App\Repositories\Log\AccessLogRepositoryInterface'
						,function($app) {
							return new AccessLogRepository(new AccessLog());
						});
	}
}