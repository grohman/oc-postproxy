<?php namespace IDesigning\PostProxy\Components;

use Cms\Classes\ComponentBase;
use Exception;
use IDesigning\PostProxy\Models\Recipient;
use IDesigning\PostProxy\Models\Rubric;
use Illuminate\Support\Facades\Event;
use Input;

class Subscriber extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Добавление в рассылку',
            'description' => 'Добавление получателей в выбранную рассылку'
        ];
    }

    public function defineProperties()
    {
        return [
            'rubric' => [
                'title' => 'Рубрика',
                'description' => 'Выберите рубрику, в которую будут добавлены получатели',
                'type' => 'dropdown',
                'required' => true
            ]
        ];
    }

    public function getRubricOptions()
    {
        return Rubric::lists('name', 'slug');
    }

    public function onSubscribe()
    {

        $email = filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL);
        if ($email == null) {
            throw new Exception('Email не определен');
        }
        $name = Input::get('name');
        if ($name == null) {
            $name = ucfirst(explode('@', $email)[ 0 ]);
        }
        $rubric = Rubric::whereSlug($this->property('rubric'))->first();
        if ($rubric == null) {
            throw new Exception('Рубрика не определена');
        }
        $recipient = Recipient::whereEmail($email)->first();
        if ($recipient == null) {
            $recipient = Recipient::create([
                'email' => $email,
                'name' => $name,
                'comment' => 'Добавился в рубрику «' . $rubric->name . '»',
            ]);
        }
        $exists = $rubric->recipients()->whereRecipientId($recipient->id)->first();
        if ($exists == null) {
            $rubric->recipients()->attach($recipient);
            Event::fire('idesigning.postproxy.subscribe', [ 'recipient' => $recipient, 'rubric' => $rubric ]);
        }

        return [ 'success' => true ];
    }
}