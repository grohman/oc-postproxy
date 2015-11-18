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
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
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

}