<?php namespace IDesigning\PostProxy\Models;

use Model;

/**
 * Rubric Model
 */
class Rubric extends Model
{

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
        'recipients' => [ 'IDesigning\PostProxy\Models\Recipient', 'table' => 'postproxy_recipient_rubric' ],
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
}