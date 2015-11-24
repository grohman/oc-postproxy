<?php namespace IDesigning\PostProxy\Models;

use Model;
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
    public $hasOne = [ ];
    public $hasMany = [ ];
    public $belongsTo = [ ];
    public $belongsToMany = [
        'recipients' => [ 'IDesigning\PostProxy\Models\Recipient', 'table' => 'postproxy_recipient_rubric', 'pivot' => 'is_unsubscribed' ],
        'channels' => [ 'IDesigning\PostProxy\Models\Channel', 'table' => 'postproxy_channel_rubric' ],
    ];
    public $morphTo = [ ];
    public $morphOne = [ ];
    public $morphMany = [ ];
    public $attachOne = [ ];
    public $attachMany = [ ];
    /**
     * @var array Guarded fields
     */
    protected $guarded = [ '*' ];
    /**
     * @var array Fillable fields
     */
    protected $fillable = [ 'name', 'slug', ];
    protected $rules = [
        'name' => 'required',
        'slug' => 'required',
    ];
}