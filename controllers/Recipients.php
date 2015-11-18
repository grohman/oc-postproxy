<?php namespace IDesigning\Postproxy\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Recipients Back-end Controller
 */
class Recipients extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    protected $requiredPermissions = ['postproxy.manage.recipients'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('IDesigning.PostProxy', 'postproxy', 'recipients');
    }
}