<?php namespace IDesigning\PostProxy\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Rubrics Back-end Controller
 */
class Rubrics extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    protected $requiredPermissions = [ 'postproxy.manage.rubrics' ];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('IDesigning.PostProxy', 'postproxy', 'rubrics');
    }
}