<?php namespace IDesigning\PostProxy\Interfaces;

interface PostProxyCollector
{


    /** Возвращает название сборщика емейлов
     * @return mixed
     */
    public function getCollectorName();

    /** Возвращает массив условий для поиска
     * @return mixed
     */
    public function getScopes();

    /** Возвращает емейлы с именами
     * @param array $scopes
     * @return mixed
     */
    public function collect(Array $scopes = [ ]); // [ [ 'johndoe@domain.tld' => 'John Doe' ], ]
}