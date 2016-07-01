<?php namespace IDesigning\PostProxy\Models;

use October\Rain\Database\Model;
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
    /**
     * @var array
     */
    public $hasMany = [];
    /**
     * @var array
     */
    public $belongsTo = [];
    /**
     * @var array
     */
    public $belongsToMany = [
        'Channels' => [ 'IDesigning\PostProxy\Models\Channel', 'table' => 'postproxy_channel_recipient' ],
        'Rubric' => [ 'IDesigning\PostProxy\Models\Rubric', 'table' => 'postproxy_recipient_rubric' ],
    ];
    /**
     * @var array
     */
    public $morphTo = [];
    /**
     * @var array
     */
    public $morphOne = [];
    /**
     * @var array
     */
    public $morphMany = [];
    /**
     * @var array
     */
    public $attachOne = [];
    /**
     * @var array
     */
    public $attachMany = [];
    /**
     * @var array
     */
    protected $rules = [
        'email' => 'required|email|unique:postproxy_recipients',
    ];
    /**
     * @var array
     */
    public $attributes = [
        'comment' => 'Добавлен админом'
    ];

    /**
     * @param      $fields
     * @param null $context
     */
    public function filterFields($fields, $context = null)
    {
        if($context == 'create' && post('Rubric') != null) {
            $fields->comment->value = 'Добавлен через рубрику «' . post('Rubric.name') .'»';
        }
    }

}