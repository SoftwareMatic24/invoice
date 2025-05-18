<?php

use App\Models\Role as ModelsRole;
use App\Models\RoleAdditionalField;

class Role
{
	static function role($id)
	{
		return ModelsRole::getRole($id);
	}

	static function roles()
	{
		return ModelsRole::getRoles()->toArray();
	}

	static function roleAdditionalFields($role)
	{
		return RoleAdditionalField::roleAdditonalFields($role)->toArray();
	}
}
