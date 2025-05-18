<?php

namespace App\Models;

require_once __DIR__."/../Plugins/payment-method/app/models/UserPaymentMethod.php";

use App\Classes\DateTime as MyDateTime;
use App\Plugins\PaymentMethods\Model\UserPaymentMethod;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	public $timestamps = false;
	
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'slug',
		'password',
		"phone",
		'about',
		'image',
		"dob",
		'status',
		'visibility',
		'role_title',
		'max_attempts',
		'create_datetime',
		'update_datetime'
	];

	protected $hidden = [
		"password",
		"remember_token",
		"max_attempts"
	];

	 function log(){
		return $this->hasMany(ActivityLog::class, "user_id", "id");
	 }

	 function address(){
		return $this->hasOne(UserAddress::class, 'user_id', 'id');
	 }

	 function details(){
		return $this->hasMany(UserDetail::class, "user_id", "id");
	 }

	 function role(){
		return $this->belongsTo(Role::class, 'role_title', 'title');
	 }

	 function paymentMethods(){
		return $this->hasMany(UserPaymentMethod::class, "user_id", "id");
	 }

	 static function basicRelation(){
		$withRegistrationLog = function($log){
			$log->where("slug", "USER_REGISTER");
		};

		return self::with(['log'=>$withRegistrationLog])
			->with('details')
			->with('address');
	 }

	/**
	 * Query: Get
	 */

	static function getUsers()
	{	
		$relation = self::basicRelation();
		return $relation->orderBy("id", "DESC")->get();
	}

	static function getUsersByIds(array $ids, null|string $status = NULL, null|string $visibility = NULL){
		$relation = self::basicRelation();
		$relation->whereIn('id', $ids);

		if(!empty($status)) $relation->where('status', $status);
		if(!empty($visibility)) $relation->where('visibility', $visibility);

		return $relation->orderBy("id", "DESC")->get();
	}

	static function getUserByEmail($email)
	{
		return User::basicRelation()->where("email", $email)->first();
	}

	static function getUsersByEmail($email){
		return User::basicRelation()->where("email", $email)->get();
	}

	static function getUsersByRole(string $role, null|string $status = NULL, null|string $visibility = NULL)
	{
		$relation = User::basicRelation()->where("role_title", $role);

		if(!empty($status)) $relation->where('status', $status);
		if(!empty($visibility)) $relation->where('visibility', $visibility);

		return $relation->get();
	}

	static function getUserBySlug(string $slug, null|string $status = NULL)
	{
		$relation = User::basicRelation()->where("slug", $slug);
		if(!empty($status)) $relation->where('status', $status);
	
		$user = $relation->first();
		
		if($user){
			$temp  = $user->details->pluck('column_value','column_name');
			$user->setRelation('details', $temp);
		}
		return $user;
	}

	static function getUsersByStatus($status)
	{
		return User::basicRelation()->where("status", $status)->get();
	}

	static function getUsersGroupByRoleTitle(){
		return self::basicRelation()->get()->groupBy('role_title');
	}

	static function getUserById($id)
	{
		return User::basicRelation()->with("details")->where("id", $id)->first();
	}
	
	/**
	 * Query: Save
	 */

	static function addUser($data)
	{

		$userData = [
			"first_name" => $data["firstName"],
			"last_name" => $data["lastName"] ?? NULL,
			"email" => $data["email"],
			"phone" => $data["phone"] ?? NULL,
			"password" => bcrypt($data["password"]),
			"role_title" => $data["roleTitle"],
			"dob" => $data["dob"] ?? NULL,
			"status" => $data["status"] ?? "inactive",
			"image" => $data["image"] ?? NULL,
			"slug"=>$data["slug"] ?? NULL,
			"create_datetime" => MyDateTime::getDateTime()
		];

		$user =  self::create($userData);
		UserDetail::addDetails($user["id"], $data["additionalDetails"] ?? []);
		return $user;
	}

	static function updateUser($userId, $data)
	{

		try {
			$user = self::find($userId);
			if(empty($user)) return NULL;

			$user->first_name = $data["firstName"];
			$user->email = $data["email"];
			$user->update_datetime = MyDateTime::getDateTime();
			
			if(isset($data["lastName"])) $user->last_name = $data["lastName"];
			if(isset($data["phone"])) $user->phone = $data["phone"];
			if(isset($data["password"]) && !empty($data["password"])) $user->password = bcrypt($data["password"]);
			if(isset($data["roleTitle"])) $user->role_title = $data["roleTitle"];
			if(isset($data["dob"])) $user->dob = $data["dob"];
			if(isset($data["status"])) $user->status = $data["status"];
			if(isset($data["image"])) $user->image = $data["image"];
			if(isset($data["slug"])) $user->slug = $data["slug"];
			
			$user->save();
			UserDetail::updateDetails($userId, $data["additionalDetails"] ?? []);
			return $user;
		}
		catch(Exception $e){
			return ["status"=>"fail", "msg"=>"Error has occured."];
		}
	}

	static function updateStatus($userId, $status)
	{
		return self::where("id", $userId)->update(["status" => $status]);
	}

	static function updatePassword($userId, $password)
	{	
		try {
			$user = self::find($userId);
			if(empty($user)) return NULL;
			$user->password = bcrypt($password);
			$user->save();
			return $user;
		}
		catch(Exception $e){
			return ["status"=>false, "msg"=>"Error has occured."];
		}
	}

	static function updateProfile($userId, $data)
	{
		$updateData = [
			"first_name" => $data["firstName"],
			"last_name" => $data["lastName"] ?? NULL,
			"email" => $data["email"],
			"phone" => $data["phone"] ?? NULL,
			"dob" => $data["dob"] ?? NULL,
			"about" => $data["about"] ?? NULL,
			"update_datetime" => MyDateTime::getDateTime()
		];

		if ($data["password"] ?? false) $updateData["password"] = bcrypt($data["password"]);
		return self::where("id", $userId)->update($updateData);
	}

	static function updateRoleAndStatus($userId, $role, $status){
		return self::where('id', $userId)->update([
			'role_title'=>$role,
			'status'=>$status
		]);
	}

	static function updateAbout($userId, $about){
		return self::where('id', $userId)->update([
			'about'=>$about
		]);
	}

	static function updateImage($userId, $url)
	{
		try {
			$user = self::find($userId);
			if(empty($user)) return NULL;

			$user->image = $url;
			$user->update_datetime = MyDateTime::getDateTime();
			$user->save();
			return $user;
		}
		catch(Exception $e){
			return ["status"=>"fail", "msg"=>$e->getMessage()];
		}
	}

	static function updateMaxAttempts($userId, $attempts){
		$user = self::getRow($userId);
		if($user) {
			$user->max_attempts = $attempts;
			$user->save();
			return $user;
		}
		return NULL;
	}
	

	/**
	 * Query: Delete
	 */

	static function deleteUser($userId)
	{
		return self::where("id", $userId)->delete();
	}

	/**
	 * Query: Row
	 */

	static function getRow($userId){
		return self::where("id", $userId)->first();
	}

}
