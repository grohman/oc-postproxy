<?php namespace IDesigning\PostProxy\Interfaces;

interface PostProxyService
{

    /** Возвращает название сервиса
     * @return mixed
     */
    public static function getServiceName();

    /** Возвращает конфиг формы авторизации в удаленном сервисе
     * @return mixed
     */
    public function getServiceAuthFormConfig();

    /** Возвращает конфиг формы редактирования настроек рассылки
     * @return mixed
     */
    public function getServiceOptionsFormConfig();

    /** Валидация данных для авторизации при сохранении модели
     * @param $data
     * @return mixed
     */
    public function validateAuth($data);

    /** Валидация формы рассылки
     * @param $data
     * @return mixed
     */
    public function validateOptions($data);

    /** Валидация рассылки перед отправкой
     * @param $data
     * @return mixed
     */
    public function validateOnSend($data);

    /** Запоминает опции рассылки перед отправкой
     * @param $data
     * @return mixed
     */
    public function setOptions($data);

    /** Метод для отправки рассылки в удаленный сервис
     * @return mixed
     */
    public function send();
}