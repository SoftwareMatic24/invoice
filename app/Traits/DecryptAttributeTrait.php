<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait DecryptAttributeTrait
{
	public function decryptAttribute($key)
	{
		if (isset($this->attributes) && !empty($this->attributes[$key])) {
			$this->attributes[$key] = Crypt::decryptString($this->attributes[$key]);
		}
	}

	public static function bootDecryptAttributeTrait()
	{
		static::retrieved(function ($model) {
			foreach ($model->attributes as $key => $value) {
				if(!empty($value) && in_array($key, $model->encryptedAttributes)) $model->attributes[$key] = Crypt::decryptString($value);
			}
		});
	}
}
