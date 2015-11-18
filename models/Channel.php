<?php namespace IDesigning\PostProxy\Models;

use Exception;
use Flash;
use Model;
use October\Rain\Database\Traits\Validation;
use Queue;

/**
 * Channel Model
 */
class Channel extends Model
{
    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'postproxy_channels';
    /**
     * @var array Relations
     */
    public $hasMany = [
    ];
    public $belongsTo = [
        'service' => [ 'IDesigning\PostProxy\Models\Service' ],
    ];
    public $belongsToMany = [
        'recipients' => [ 'IDesigning\PostProxy\Models\Recipient', 'table' => 'postproxy_channel_recipient' ],
    ];
    public $attributes = [
        'state' => 'Готово к отправке'
    ];
    /**
     * @var array Guarded fields
     */
    protected $guarded = [ '*' ];
    /**
     * @var array Fillable fields
     */
    protected $fillable = [ 'service_id', 'options', 'state' ];
    protected $jsonable = [ 'options' ];
    protected $rules = [
        'name' => 'required',
        'service_id' => 'required',
    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if ($model->getAttribute('service_id') == null) {
                return;
            }

            $instance = $model->service->getServiceInstance();
            if ($instance) {
                return $instance->validateOptions($model->getAttribute('options'));
            }
        });
    }

    public function send()
    {
        $data = $this->toArray();
        $data[ 'options' ] = $this->getAttribute('options');
        $data[ 'recipients' ] = $this->recipients()->get()->toArray();
        $data[ 'auth' ] = $this->service()->first()->auth;
        if (isset($data[ 'recipients' ][ 0 ]) == false) {
            throw new Exception('Не выбраны получатели рассылки');
        }
        $instance = $this->service->getServiceInstance();
        $instance->validateOnSend($data);
        Queue::push(function ($job) use ($instance) {
            $instance->send();
            $job->delete();
        });
        $state = 'Рассылка передана для отправки';
        $this->update([ 'state' => $state ]);
        Flash::success($state);

        return true;
    }


    public function getServiceIdOptions()
    {
        return [ '' => 'Не выбран' ] + Service::lists('name', 'id');
    }

    public function loadCustomForm()
    {
        if ($this->getAttribute('service_id') == null) {
            return;
        }

        $instance = $this->service->getServiceInstance();
        if ($instance) {
            return $instance->getServiceOptionsFormConfig($this->service);
        }

        return;
    }

    public function toJson($options = 0)
    {
        $data = $this->toArray();
        $data = [ 'asd' ];

        return json_encode($data, $options);
    }
}