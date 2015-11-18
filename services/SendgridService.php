<?php namespace IDesigning\Postproxy\Services;

use Carbon\Carbon;
use Exception;
use IDesigning\PostProxy\Interfaces\PostProxyService;
use Illuminate\Support\Facades\Validator;

class SendgridServiceException extends Exception
{
}

class SendgridService implements PostProxyService
{

    protected $options;

    /** Возвращает конфиг формы авторизации в удаленном сервисе
     * @return mixed
     */
    public function getServiceAuthFormConfig()
    {
        return [
            'fields' => [
                'apikey' => [
                    'label' => 'Ключ апи',
                    'type' => 'text',
                    'required' => true
                ]
            ]
        ];
    }

    /** Валидация данных для авторизации при сохранении модели
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function validateAuth($data)
    {
        return $this->validator($data, [
            'apikey' => 'required',
        ]);
    }

    protected function validator($data, $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new SendgridServiceException($validator->getMessageBag()->first());
        }

        return true;
    }

    /** Возвращает конфиг формы редактирования настроек рассылки
     * @return mixed
     */
    public function getServiceOptionsFormConfig()
    {

        return [
            'fields' => [
                'subject' => [
                    'label' => 'Тема письма',
                    'type' => 'text',
                    'required' => true
                ],
                'from_email' => [
                    'label' => 'От (email)',
                    'type' => 'text',
                    'required' => true,
                    'span' => 'left',
                ],
                'from_name' => [
                    'label' => 'От (имя)',
                    'type' => 'text',
                    'required' => true,
                    'span' => 'right',
                ],
                'html_template' => [
                    'label' => 'Шаблон',
                    'type' => 'richeditor',
                    'required' => true
                ],
                'text_template' => [
                    'label' => 'Текстовый шаблон',
                    'type' => 'textarea'
                ],
                'send_at_date' => [
                    'label' => 'День отправки',
                    'type' => 'datepicker',
                    'minDate' => Carbon::now(),
                    'mode' => 'date',
                    'span' => 'left',
                    'required' => true,
                ],
                'send_at_time' => [
                    'label' => 'Время отправки',
                    'type' => 'datepicker',
                    'mode' => 'time',
                    'span' => 'right',
                    'required' => true,
                ],
            ]
        ];
    }

    /** Валидация рассылки перед отправкой
     * @param $data
     * @return mixed
     * @throws SendgridServiceException
     */
    public function validateOnSend($data)
    {
        $options = $data[ 'options' ];
        $this->validateOptions($options);
        $date = Carbon::createFromFormat('Y-m-d H:i', $options[ 'send_at_date' ] . ' ' . $options[ 'send_at_time' ]);
        if (Carbon::now()->gt($date) == true) {
            throw new SendgridServiceException('Дата отправки не может быть в прошлом');
        }
        $options[ 'send_at' ] = $date;
        $this->setOptions([
            'sendgrid' => $options,
            'recipients' => $data[ 'recipients' ],
            'auth' => $data[ 'auth' ][ 'apikey' ]
        ]);

        return true;
    }

    /** Валидация формы рассылки
     * @param $data
     * @return mixed
     */
    public function validateOptions($data)
    {
        return $this->validator($data, [
            'subject' => 'required',
            'from_email' => 'required',
            'from_name' => 'required',
            'html_template' => 'required',
            'send_at_date' => 'required',
            'send_at_time' => 'required',
        ]);
    }

    /** Запоминает опции рассылки перед отправкой
     * @param $data
     * @return mixed
     */
    public function setOptions($data)
    {
        $this->options = $data;
    }

    /** Метод для отправки рассылки в удаленный сервис
     * @return mixed
     * @throws SendgridServiceException
     */
    public function send()
    {
        require plugins_path() . '/idesigning/postproxy/vendor/sendgrid/sendgrid/lib/SendGrid.php';
        $sendgrid = new \SendGrid($this->options[ 'auth' ]);
        $email = new \SendGrid\Email();
        $options = $this->options[ 'sendgrid' ];
        $email->setFrom($options[ 'from_email' ])
            ->setFromName($options[ 'from_name' ])
            ->setSendAt($options[ 'send_at' ]->timestamp)
            ->setSubject($options[ 'subject' ])
            ->setText($options[ 'text_template' ])
            ->setHtml($options[ 'html_template' ]);

        foreach ($this->options[ 'recipients' ] as $value) {
            $email->addSmtpapiTo($value[ 'email' ], $value[ 'name' ]);
        }

        try {
            $sendgrid->send($email);
        } catch(Exception $e) {
            throw new SendgridServiceException($e->getMessage());
        }

        return true;
    }
}