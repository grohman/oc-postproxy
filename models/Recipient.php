<?php namespace IDesigning\PostProxy\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Recipient Model
 */
class Recipient extends Model
{

    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'postproxy_recipients';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [ 'name', 'email', 'comment' ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'Channels' => [ 'IDesigning\PostProxy\Models\Channel', 'table' => 'postproxy_channel_recipient' ],
        'Rubric' => [ 'IDesigning\PostProxy\Models\Rubric', 'table' => 'postproxy_recipient_rubric' ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
    protected $rules = [
        'email' => 'required|email'
    ];
    public $attributes = [
        'comment' => 'Добавлен админом'
    ];

    public function filterFields($fields, $context = null)
    {
        if($context == 'create' && post('Rubric') != null) {
            $fields->comment->value = 'Добавлен через рубрику «' . post('Rubric.name') .'»';
        }
    }

}