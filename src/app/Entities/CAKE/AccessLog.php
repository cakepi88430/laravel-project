<?php

namespace App\Entities\CAKE;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use \App\Entities\ModelStdClassTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // protected $connection = "main";
    protected $table = 'access_logs';

    protected $primaryKey = 'id';

    protected $guarded = array();

    protected $fillable = ['user_id', 'token_id', 'access_ip', 'method'
                          ,'access_url', 'access_url', 'created_at', 'created_at'];

}