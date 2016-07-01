<?php namespace IDesigning\PostProxy\Models;

use Exception;
use IDesigning\PostProxy\Interfaces\PostProxyService;
use October\Rain\Database\Model;
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
        'channels' => ['IDesigning\PostProxy\Models\Channel'],
    ];
    /**
     * @var array
     */
    protected $rules = [
        'name'     => 'required',
        'api_name' => 'required',
    ];
    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];
    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'api_name',
        'auth',
    ];
    /**
     * @var array
     */
    protected $jsonable = ['auth'];
    /**
     * @var array
     */
    protected $apis = [];

    /**
     *
     */
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

    /**
     * @return mixed|string
     */
    public function getCurrentApiName()
    {
        $apis = $this->getApiNameOptions();
        if (isset($apis[$this->api_name])) {
            return $apis[$this->api_name];
        }
        return '---';
    }

    /**
     * @param null $key
     * @return array
     */
    public function getApiNameOptions($key = null)
    {
        if ($this->apis == null) {
            $servicesArray = [];
            $services = config()->get('idesigning.postproxy::services');
            foreach ($services as $key => $value) {
                try {
                    $servicesArray[$value] = $value::getServiceName();
                } catch (Exception $e) {

                }
            }
            $this->apis = ['' => 'Не выбран'] + $servicesArray;
        }

        return $this->apis;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function loadCustomForm()
    {
        $service = $this->getAttribute('api_name');
        $instance = $this->getServiceInstance($service);
        if ($instance) {
            return $instance->getServiceAuthFormConfig($this);
        }
    }

    /**
     * @param null $service
     * @throws Exception
     */
    public function getServiceInstance($service = null)
    {
        if (null == $service) {
            if ($this->api_name == null) {
                return;
            } else {
                $service = $this->api_name;
            }
        }

        $services = config()->get('idesigning.postproxy::services');
        $className = null;
        foreach ($services as $configService) {
            if ($configService == $service) {
                $className = $service;
                break;
            }
        }
        if (class_exists($className) == false) {
            return;
        }
        $instance = new $className;
        if (($instance instanceof PostProxyService) == false) {
            throw new Exception('Class ' . $className . ' should implement PostProxyService');
        }

        return $instance;
    }
}