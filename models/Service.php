<?php namespace IDesigning\PostProxy\Models;

use Exception;
use IDesigning\PostProxy\Interfaces\PostProxyService;
use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Service Model
 */
class Service extends Model
{

    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'postproxy_services';
    /**
     * @var array Relations
     */
    public $hasMany = [
        'channels' => [ 'IDesigning\PostProxy\Models\Channel' ],
    ];
    protected $rules = [
        'name' => 'required',
        'api_name' => 'required',
    ];
    /**
     * @var array Guarded fields
     */
    protected $guarded = [ '*' ];
    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'api_name',
        'auth'
    ];
    protected $jsonable = [ 'auth' ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $service = $model->getAttribute('api_name');
            $instance = $model->getServiceInstance($service);
            if ($instance) {
                return $instance->validateAuth($model->getAttribute('auth'));
            }
        });
    }

    public function getApiNameOptions()
    {
        $servicesArray = [ ];
        $services = config()->get('idesigning.postproxy::services');
        foreach ($services as $key => $value) {
            $servicesArray[ $key ] = $value[ 'label' ];
        }

        return [ '' => 'Не выбран' ] + $servicesArray;
    }

    public function loadCustomForm()
    {
        $service = $this->getAttribute('api_name');
        $instance = $this->getServiceInstance($service);
        if ($instance) {
            return $instance->getServiceAuthFormConfig($this);
        }
    }

    public function getServiceInstance($service = null)
    {
        if (null == $service) {
            if($this->api_name == null) {
                return;
            } else {
                $service = $this->api_name;
            }
        }

        $description = config()->get('idesigning.postproxy::services')[ $service ];
        $className = $description[ 'class' ];
        if (class_exists($className) == false) {
            throw new Exception('Class ' . $className . ' not found');
        }
        $instance = new $className;
        if (($instance instanceof PostProxyService) == false) {
            throw new Exception('Class ' . $className . ' should implement PostProxyService');
        }

        return $instance;
    }
}