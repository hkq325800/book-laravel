<?php

use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class BookBasic extends Eloquent implements RemindableInterface {

	use RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'bookbasic';
	public $timestamps =false;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

}
