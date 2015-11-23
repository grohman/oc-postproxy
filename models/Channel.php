<?php namespace IDesigning\PostProxy\Models;

use Exception;
use IDesigning\PostProxy\Interfaces\PostProxyCollector;
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
        'rubrics' => [ 'IDesigning\PostProxy\Models\Rubric', 'table' => 'postproxy_channel_rubric' ],
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
    protected $fillable = [ 'service_id', 'options', 'collectors', 'state' ];
    protected $jsonable = [ 'options', 'collectors' ];
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

    public function getTotalRecipients()
    {
        $recipients = [ ];
        $result = 0;
        $this->rubrics()->get()->each(function ($rubric) use (&$recipients, &$result) {
            $rubric->recipients()->get()->each(function ($recipient) use (&$recipients, &$result) {
                if (isset($recipients[ $recipient->email ]) == false) {
                    $recipients[ $recipient->email ] = $recipient->name;
                    $result++;
                }
            });
        });

        $this->recipients()->get()->each(function ($recipient) use (&$recipients, &$result) {
            if (isset($recipients[ $recipient->email ]) == false) {
                $recipients[ $recipient->email ] = $recipient->name;
                //dd($recipient);
                $result++;
            }
        });

        return $result;
    }

    public function send()
    {
        $data = $this->toArray();
        $data[ 'options' ] = $this->getAttribute('options');
        $recipients = [ ];

        $this->rubrics()->get()->each(function ($rubric) use (&$recipients) {
            $rubric->recipients()->get()->each(function ($recipient) use (&$recipients) {
                if (isset($recipients[ $recipient->email ]) == false) {
                    $recipients[ $recipient->email ] = $recipient->name;
                }
            });
        });

        $this->recipients()->get()->each(function ($recipient) use (&$recipients) {
            if (isset($recipients[ $recipient->email ]) == false) {
                $recipients[ $recipient->email ] = $recipient->name;
            }
        });

        $data[ 'recipients' ] = $recipients;
        $data[ 'auth' ] = $this->service()->first()->auth;
        if (empty($data[ 'recipients' ]) == true) {
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

        return $state;
    }


    public function getServiceIdOptions()
    {
        return [ '' => 'Не выбран' ] + Service::lists('name', 'id');
    }

    public function loadOptionsForm()
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

    public function loadCollectorsForm()
    {
        $collectors = config()->get('idesigning.postproxy::collectors');
        if (empty($collectors)) {
            return [ ];
        }

        $result = [ ];
        foreach ($collectors as $key => $item) {
            $instance = new $item;
            if (($instance instanceof PostProxyCollector) == false) {
                continue;
            }

            $result[ $item ] = [
                'tab' => $instance->getCollectorName(),
                'type' => 'checkboxlist',
                'options' => [ ],


            ];

            $result[ $item . '_collectButton' ] = [
                'tab' => $instance->getCollectorName(),
                'type' => 'partial',
                'collector' => $item,
                'path' => '@/plugins/idesigning/postproxy/controllers/channels/_collectbutton.htm'
            ];

            foreach ($instance->getScopes() as $scopeKey => $value) {
                $result[ $item ][ 'options' ][ $scopeKey ] = $value[ 'label' ];
            }
        }

        return [
            'secondaryTabs' => [ 'stretch' => true, 'fields' => $result ]
        ];
    }

    public function collect($collector)
    {
        $instance = null;
        $scopes = [ ];
        foreach ($this->collectors as $name => $value) {
            if ($name == $collector) {
                $instance = new $collector;
                if (($instance instanceof PostProxyCollector) == false) {
                    continue;
                }
                if ($value != 0) {
                    $scopes = $value;
                }
                break;
            }

        }

        if ($instance != null) {
            $attachIds = [ ];
            $emails = $instance->collect($scopes);
            foreach ($emails as $email => $name) {
                $exists = Recipient::whereEmail($email)->first();
                if ($exists == null) {
                    $newRecipient = Recipient::create([
                        'name' => $name,
                        'email' => $email,
                        'comment' => 'Добавлен сборщиком «' . $instance->getCollectorName() . '»'
                    ]);
                    $attachIds[] = $newRecipient->id;
                } else {
                    $attached = $this->recipients()->whereRecipientId($exists->id)->count();
                    if ($attached == 0) {
                        $attachIds[] = $exists->id;
                    }
                }
            }

            if (isset($attachIds[ 0 ])) {
                $this->recipients()->attach($attachIds);
            }

            return true;

        }

        return false;
    }
}