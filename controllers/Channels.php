<?php namespace IDesigning\PostProxy\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use IDesigning\PostProxy\Models\Channel;

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

    protected $requiredPermissions = [ 'postproxy.manage.channels' ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('IDesigning.PostProxy', 'postproxy', 'channels');
    }

    public function onSend($channelId)
    {
        $data = \Input::get('Channel');
        $channel = Channel::find($channelId);
        $channel->update($data);
        $state = $channel->send();
        Flash::success($state);

        return $this->makeRedirect();
    }

    public function onCollect($channelId)
    {
        $data = \Input::get('Channel');

        $channel = Channel::find($channelId);
        $channel->update($data);

        $collector = urldecode(\Input::get('collector'));
        $channel->collect($collector);
        Flash::success('Сбор адресов завершен');
        return $data;
    }
}