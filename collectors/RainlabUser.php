<?php namespace IDesigning\PostProxy\Collectors;

use Carbon\Carbon;
use Exception;
use IDesigning\PostProxy\Interfaces\PostProxyCollector;
use RainLab\User\Models\User;

class RainlabUser implements PostProxyCollector
{

    public function __construct()
    {
        if (class_exists('\RainLab\User\Models\User') == false) {
            throw new Exception('RainLab.User not found');
        }
    }

    /** Возвращает название сборщика емейлов
     * @return mixed
     */
    public function getCollectorName()
    {
        return 'Пользователи сайта';
    }

    /** Возвращает емейлы с именами
     * @param array $scopes
     * @return mixed
     */
    public function collect(Array $scopes = [])
    {
        $query = User::select(\DB::raw('CONCAT(name, " ", surname) as name'), 'email');
        if (isset($scopes[0])) {
            foreach ($scopes as $scope) {
                $fn = $this->getScopes()[$scope]['scope'];
                $query = $fn($query);
            }
        }
        return $query->get()->lists('name', 'email');
    }

    /** Возвращает массив условий для поиска
     * @return mixed
     */
    public function getScopes()
    {
        return [
            'active'     => [
                'label' => 'Активированные',
                'scope' => function ($query) {
                    return $query->where('is_activated', '=', 1);
                },
            ],
            'today'      => [
                'label' => 'Добавленные сегодня',
                'scope' => function ($query) {
                    return $query->where('created_at', '>=', Carbon::now()->startOfDay());
                },
            ],
            'yesterday'  => [
                'label' => 'Добавленные вчера',
                'scope' => function ($query) {
                    return $query->where('created_at', '>=', Carbon::now()->subDay()->startOfDay());
                },
            ],
            'lastWeek'   => [
                'label' => 'Добавленные за неделю',
                'scope' => function ($query) {
                    return $query->where('created_at', '>=', Carbon::now()->subWeek()->startOfDay());
                },
            ],
            'lastMonth'  => [
                'label' => 'Добавленные за месяц',
                'scope' => function ($query) {
                    return $query->where('created_at', '>=', Carbon::now()->subMonth()->startOfDay());
                },
            ],
            'neverLogin' => [
                'label' => 'Никогда не заходили',
                'scope' => function ($query) {
                    return $query->whereNull('last_login');
                },
            ],
            'weekLogin'  => [
                'label'   => 'Не заходили больше недели',
                'trigger' => [
                    'action'    => 'disable',
                    'field'     => 'neverLogin',
                    'condition' => 'checked',
                ],
                'scope'   => function ($query) {
                    return $query->where('last_login', '<=', Carbon::now()->subWeek());
                },
            ],
        ];
    }
}