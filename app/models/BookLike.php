<?php

use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class BookLike extends Eloquent implements RemindableInterface {

	use RemindableTrait;
	use SoftDeletingTrait;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'booklike';
	public $timestamps =false;
	public function bookbasic(){
		return $this->hasOne('BookBasic','id','book_kind');
	}
	public function user()
    {
    	return $this->hasOne('User','user_id','user_id');
    }
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

}
