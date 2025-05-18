<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\DB;

return new class {
	public function run(){
		$classifications = [
			[
				'name'=>'Default',
				'slug'=>'default',
				'create_datetime'=>DateTime::getDateTime()
			]
		];

		DB::table('subscription_package_classifications')->insert($classifications);
	}
}

?>