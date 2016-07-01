<?php namespace IDesigning\PostProxy\Collectors;

use IDesigning\PostProxy\Interfaces\PostProxyCollector;
use IDesigning\PostProxy\Models\Recipient;

class PostProxyRecipients implements PostProxyCollector
{

    /** Возвращает название сборщика емейлов
     * @return mixed
     */
    public function getCollectorName()
    {
        return 'Все получатели';
    }

    /** Возвращает емейлы с именами
     * @param array $scopes
     * @return mixed
     */
    public function collect(Array $scopes = [])
    {
        $query = Recipient::select('name', 'email');
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

        $comments = Recipient::distinct()->select('comment')->lists('comment');
        $result = [];
        foreach ($comments as $comment) {
            $result[$comment] = [
                'label' => $comment,
                'scope' => function ($query) use ($comment) {
                    return $query->where('comment', '=', $comment);
                },
            ];
        }
        if (empty($result)) {
            $result[] = [
                'label' => 'Все',
                'scope' => function ($query) {
                    return $query;
                },
            ];
        }
        return $result;
    }
}