<?php namespace IDesigning\Postproxy\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
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

    protected $requiredPermissions = ['postproxy.manage.channels'];

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
        Channel::find($channelId)->send();
        return $this->makeRedirect();
    }
}