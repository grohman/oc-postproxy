<?php namespace IDesigning\PostProxy;

use Backend;
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
            'name'        => 'PostProxy',
            'description' => 'Плагин для управления емейл-рассылками\'',
            'author'      => 'Daniel Podrabinek',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     */
    public function registerPermissions()
    {
        return [
            'postproxy.manage.services' => ['label' => 'Управление сервисами рассылок', 'tab' => 'PostProxy'],
            'postproxy.manage.rubrics' => ['label' => 'Управление рубриками', 'tab' => 'PostProxy' ],
            'postproxy.manage.recipients' => ['label' => 'Управление получателями рассылок', 'tab' => 'PostProxy'],
            'postproxy.manage.channels' => ['label' => 'Управление рассылками', 'tab' => 'PostProxy' ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'postproxy' => [
                'label'       => 'Email-рассылки',
                'url'         => Backend::url('idesigning/postproxy/channels'),
                'icon'        => 'icon-envelope',
                'permissions' => ['postproxy.manage.channels'],
                'sideMenu' => [
                    'channels' => [
                        'label'       => 'Рассылки',
                        'icon'        => 'icon-envelope-o',
                        'url'         => Backend::url('idesigning/postproxy/channels'),
                        'permissions' => ['postproxy.manage.channels'],
                    ],
                    'recipients' => [
                        'label'       => 'Получатели',
                        'icon'        => 'icon-users',
                        'url'         => Backend::url('idesigning/postproxy/recipients'),
                        'permissions' => ['postproxy.manage.recipients']
                    ],
                    'rubrics' => [
                        'label' => 'Рубрики',
                        'icon' => 'icon-cubes',
                        'url'         => Backend::url('idesigning/postproxy/rubrics'),
                        'permissions' => ['postproxy.manage.rubrics']
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
                'permissions' => ['postproxy.manage.services']
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'IDesigning\PostProxy\FormWidgets\ServicesWidget' => [
                'label' => 'Services options',
                'code'  => 'servicesWidget'
            ]
        ];
    }
}