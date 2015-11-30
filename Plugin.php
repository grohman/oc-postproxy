<?php namespace IDesigning\PostProxy;

use Backend;
use IDesigning\PostProxy\Models\Recipient;
use System\Classes\PluginBase;

/**
 * PostProxy Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'PostProxy',
            'description' => 'Плагин для управления емейл-рассылками\'',
            'author' => 'Daniel Podrabinek',
            'icon' => 'icon-leaf'
        ];
    }

    public function boot()
    {
        if ($this->app->runningInBackend() == false) {
            if (class_exists('\Grohman\Tattler\Lib\Inject')) {
                Recipient::extend(function ($model) {
                    if ($model->isClassExtendedWith('\Grohman\Tattler\Lib\Inject') == false) {
                        $model->extendClassWith('\Grohman\Tattler\Lib\Inject');
                    }
                });
            }
        }
    }

    /**
     * Registers any back-end permissions used by this plugin.
     */
    public function registerPermissions()
    {
        return [
            'postproxy.manage.services' => [ 'label' => 'Управление сервисами рассылок', 'tab' => 'PostProxy' ],
            'postproxy.manage.rubrics' => [ 'label' => 'Управление рубриками', 'tab' => 'PostProxy' ],
            'postproxy.manage.recipients' => [ 'label' => 'Управление получателями рассылок', 'tab' => 'PostProxy' ],
            'postproxy.manage.channels' => [ 'label' => 'Управление рассылками', 'tab' => 'PostProxy' ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'postproxy' => [
                'label' => 'Email-рассылки',
                'url' => Backend::url('idesigning/postproxy/channels'),
                'icon' => 'icon-envelope',
                'permissions' => [ 'postproxy.manage.channels' ],
                'sideMenu' => [
                    'rubrics' => [
                        'label' => 'Рубрики',
                        'icon' => 'icon-cubes',
                        'url' => Backend::url('idesigning/postproxy/rubrics'),
                        'permissions' => [ 'postproxy.manage.rubrics' ]
                    ],
                    'channels' => [
                        'label' => 'Рассылки',
                        'icon' => 'icon-envelope-o',
                        'url' => Backend::url('idesigning/postproxy/channels'),
                        'permissions' => [ 'postproxy.manage.channels' ],
                    ],
                    'recipients' => [
                        'label' => 'Получатели',
                        'icon' => 'icon-users',
                        'url' => Backend::url('idesigning/postproxy/recipients'),
                        'permissions' => [ 'postproxy.manage.recipients' ]
                    ],

                ]

            ]
        ];
    }

    public function registerSettings()
    {
        return [
            'services' => [
                'label' => 'Сервисы',
                'description' => 'Управление сервисами email-рассылок',
                'category' => 'PostProxy',
                'icon' => 'icon-database',
                'url' => Backend::url('idesigning/postproxy/services'),
                'order' => 500,
                'keywords' => 'postproxy services',
                'permissions' => [ 'postproxy.manage.services' ]
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'IDesigning\PostProxy\FormWidgets\ServicesWidget' => [
                'label' => 'Services options',
                'code' => 'servicesWidget'
            ]
        ];
    }

    public function registerComponents()
    {
        return [
            'IDesigning\PostProxy\Components\Subscriber' => 'postproxy_subscriber'
        ];
    }
}