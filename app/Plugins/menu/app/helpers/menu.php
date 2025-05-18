<?php

namespace App\Plugins\Menu\Helpers;

use App\Plugins\Menu\Controller\MenuController;

class Menu {

	static function menuByName(string $name){
		return (new MenuController())->getFormatMenuByName($name);
	}

}

?>