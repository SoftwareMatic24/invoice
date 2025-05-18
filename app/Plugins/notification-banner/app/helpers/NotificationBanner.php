<?php

namespace App\Plugins\NotificationBanner\Helpers;

use App\Plugins\NotificationBanner\Models\NotificationBanner as ModelsNotificationBanner;

class NotificationBanner {


	/**
	 * View
	 */

	static function notificationBannerWebView(){
		$notificationBanners = ModelsNotificationBanner::getNotificationBanners('active', 'web')->toArray();
		return self::notificaitonBannersView($notificationBanners);
	}

	static function notificationBannerPortalView(){
		$notificationBanners = ModelsNotificationBanner::getNotificationBanners('active', 'portal')->toArray();
		return self::notificaitonBannersView($notificationBanners, 'portal');
	}

	static function notificaitonBannersView(array $notificationBanners, string $class = ''){
		
		$notificationBannerHTML = "";
		foreach($notificationBanners as $notificationBanner){

			$style = self::notificationBannerStyle($notificationBanner);

			$notificationBannerHTML .= "<div class='notification-banner $class' style='$style'>";
			$notificationBannerHTML .= "<div class='notification-banner-inner'>";
			$notificationBannerHTML .= "<p class='notification-banner-text'>".$notificationBanner['text']."</p>";
			$notificationBannerHTML .= "</div>";
			$notificationBannerHTML .= "</div>";
		}
		
		return "<div class='notification-banner-container'>".$notificationBannerHTML."</div>";

	}

	// Util

	static function notificationBannerStyle($notificationBanner){
		$style = "";
		if ($notificationBanner["style"] !== NULL) {
			$styleArr = json_decode($notificationBanner["style"], true);
			if ($styleArr["bgColor"] ?? false) $style .= " background-color: " . $styleArr["bgColor"] . "; ";
			if ($styleArr["color"] ?? false) $style .= " color: " . $styleArr["color"] . "; ";
		}
		return $style;
	}


	/**
	 * Get
	 */
	
	static function notificationBanner($id){
		$notificationBanner = ModelsNotificationBanner::getNotificationBanner($id);
		return empty($notificationBanner) ? NULL : $notificationBanner->toArray();
	}

	static function notificationBanners(){
		return ModelsNotificationBanner::getNotificationBanners()->toArray();
	}

}

?>