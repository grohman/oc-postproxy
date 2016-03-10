<?php namespace IDesigning\PostProxy\Controllers;

use Backend\Classes\Controller;
use Backend\Facades\BackendAuth;
use BackendMenu;
use Event;
use Flash;
use IDesigning\PostProxy\Models\Channel as PostproxyChannel;
use IDesigning\PostProxy\Models\Service as PostproxyService;

/**
 * Channels Back-end Controller
 */
class Channels extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    protected $requiredPermissions = ['postproxy.manage.channels'];
    protected $defaultService;

    public function __construct()
    {
        parent::__construct();
        $this->defaultService = config()->get('idesigning.postproxy::defaultService');

        if (BackendAuth::getUser()->hasAccess('postproxy.manage.recipients') == false) {
            $collectors = array_where(config()->get('idesigning.postproxy::collectors'), function ($key, $value) {
                if ($value != 'IDesigning\\PostProxy\\Collectors\\PostProxyRecipients') {
                    return $value;
                }
            });
            config()->set('idesigning.postproxy::collectors', $collectors);
        }

        BackendMenu::setContext('IDesigning.PostProxy', 'postproxy', 'channels');
    }

    public function onSend($channelId)
    {
        $data = post('Channel');
        $channel = PostproxyChannel::find($channelId);
        $channel->update($data);
        $state = $channel->send();
        Flash::success($state);

        return $this->makeRedirect();
    }

    public function onCollect($channelId)
    {
        $data = post('Channel');

        $channel = PostproxyChannel::find($channelId);
        $channel->update($data);

        $collector = urldecode(post('collector'));
        $channel->collect($collector);
        Flash::success('Сбор адресов завершен');
        return $data;
    }

    public function listExtendColumns($list)
    {
        if (BackendAuth::getUser()->hasAccess('postproxy.manage.services') == false) {
            $list->removeColumn('service_id');
        }
    }

    public function formExtendModel($model)
    {
        if (BackendAuth::getUser()->hasAccess('postproxy.manage.services') == false && $this->defaultService) {
            $service = PostproxyService::whereName($this->defaultService)->first();
            $model->service_id = $service->id;
        }
        return $model;
    }

    public function formExtendFields($form, $fields)
    {
        if (BackendAuth::getUser()->hasAccess('postproxy.manage.services') == false && $this->defaultService) {
            $fields['service_id']->cssClass = 'hidden';
        }

        if (BackendAuth::getUser()->hasAccess('postproxy.manage.rubrics') == false) {
            if(isset($fields['_rubrics'])) {
                $form->removeField('_rubrics');
                $form->removeField('rubrics');
            }

            if (isset($fields['_recipients'])) {
                $recipientsSection = $form->getField('_recipients');
                $recipientsSection->label = 'Получатели';
                $recipients = $form->getField('recipients');
                $recipients->label = 'Список получателей';
            }
        }
    }

    public function relationExtendManageWidget($widget, $field)
    {
        if ($field == 'recipients') {
            if (BackendAuth::getUser()->hasAccess('postproxy.manage.rubrics') == false) {
                if ($widget instanceof \Backend\Widgets\Form) {
                    Event::listen('backend.form.extendFields', function ($widget) {
                        $comment = $widget->getField('comment');
                        $comment->hidden = true;
                    });
                } else if ($widget instanceof \Backend\Widgets\Lists) {
                    Event::listen('backend.list.extendColumns', function ($widget) {
                        $widget->removeColumn('comment');
                    });
                }
            }
        }
    }
}