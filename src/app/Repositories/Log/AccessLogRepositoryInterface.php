<?php
namespace App\Repositories\Log;

interface AccessLogRepositoryInterface
{
	public function LogAccess($data);
}