<?php
namespace App\Repositories\Log;

use Illuminate\Database\Eloquent\Model;
use \stdClass;
use Illuminate\Support\Facades\Log;

class AccessLogRepository implements AccessLogRepositoryInterface
{

	protected $_accessLogModel;
	protected $_fieldNames = array('id','user_id','session_id','token_id'
								  ,'access_ip','access_url','query_str', 'created_at', 'updated_at');

	public function __construct(Model $access_log)
	{
		$this->_accessLogModel = $access_log;
	}

	public function LogAccess($data)
	{
		try {
			if (is_array($data))
			{
				$data['created_at'] = \Carbon\Carbon::now();
				$data['updated_at'] = \Carbon\Carbon::now();
				$this->_accessLogModel->insert(array($data));
			}
			else
			{
				if(is_object($data))
				{
					$access_log = $this->_accessLogModel->newFromStd($data);
					$access_log->save();
				}
			}
		} catch (\Exception $e) {
			Log::warning('AccessLog failed ['.json_encode($data).']');
		}
	}

}