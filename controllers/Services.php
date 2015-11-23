<?php namespace IDesigning\PostProxy\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use System\Classes\SettingsManager;


/**
 * Services Back-end Controller
 */
class Services extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    protected $requiredPermissions = ['postproxy.manage.services'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('iDesigning.PostProxy', 'services');
    }
}