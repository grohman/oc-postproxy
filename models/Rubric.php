<?php namespace IDesigning\PostProxy\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Validation;

/**
 * Rubric Model
 */
class Rubric extends Model
{
    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'postproxy_rubrics';
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
        'recipients' => ['IDesigning\PostProxy\Models\Recipient', 'table' => 'postproxy_recipient_rubric', 'pivot' => 'is_unsubscribed'],
        'channels'   => ['IDesigning\PostProxy\Models\Channel', 'table' => 'postproxy_channel_rubric'],
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
     * @var array Guarded fields
     */
    protected $guarded = ['*'];
    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name', 'slug',];
    /**
     * @var array
     */
    protected $rules = [
        'name' => 'required',
        'slug' => 'required',
    ];
}