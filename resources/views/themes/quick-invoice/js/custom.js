// JavaScript Document


$(window).on('load', function () {

	"use strict";

	/*----------------------------------------------------*/
	/*	Preloader
	/*----------------------------------------------------*/

	var preloader = $('#loading'),
		loader = preloader.find('#loading-center');
	loader.fadeOut();
	preloader.delay(400).fadeOut('slow');


	/*----------------------------------------------------*/
	/*	Modal Window
	/*----------------------------------------------------*/

	setTimeout(function () {
		$(".modal:not(.auto-off)").modal("show");
	}, 3600);

});


$(window).on('scroll', function () {

	"use strict";

	/*----------------------------------------------------*/
	/*	Navigtion Menu Scroll
	/*----------------------------------------------------*/

	var b = $(window).scrollTop();

	if (b > 80) {
		$(".wsmainfull").addClass("scroll");
	} else {
		$(".wsmainfull").removeClass("scroll");
	}

});


$(document).ready(function () {

	"use strict";


	/*----------------------------------------------------*/
	/*	Mobile Menu Toggle
	/*----------------------------------------------------*/

	if ($(window).outerWidth() < 992) {
		$('.wsmenu-list li.nl-simple, .wsmegamenu li, .sub-menu li').on('click', function () {
			$('body').removeClass("wsactive");
			$('.sub-menu').slideUp('slow');
			$('.wsmegamenu').slideUp('slow');
			$('.wsmenu-click').removeClass("ws-activearrow");
			$('.wsmenu-click02 > i').removeClass("wsmenu-rotate");
		});
	}

	if ($(window).outerWidth() < 992) {
		$('.wsanimated-arrow').on('click', function () {
			$('.sub-menu').slideUp('slow');
			$('.wsmegamenu').slideUp('slow');
			$('.wsmenu-click').removeClass("ws-activearrow");
			$('.wsmenu-click02 > i').removeClass("wsmenu-rotate");
		});
	}


	/*----------------------------------------------------*/
	/*	Accordion
	/*----------------------------------------------------*/

	$(".accordion > .accordion-item.is-active").children(".accordion-panel").slideDown();

	$(".accordion > .accordion-item").on('click', function () {
		$(this).siblings(".accordion-item").removeClass("is-active").children(".accordion-panel").slideUp();
		$(this).toggleClass("is-active").children(".accordion-panel").slideToggle("ease-out");
	});

	/*----------------------------------------------------*/
	/*	Show Password
	/*----------------------------------------------------*/

	var showPass = 0;
	$('.btn-show-pass').on('click', function () {
		if (showPass == 0) {
			$(this).next('input').attr('type', 'text');
			$(this).find('span.eye-pass').removeClass('flaticon-visibility');
			$(this).find('span.eye-pass').addClass('flaticon-invisible');
			showPass = 1;
		}
		else {
			$(this).next('input').attr('type', 'password');
			$(this).find('span.eye-pass').addClass('flaticon-visibility');
			$(this).find('span.eye-pass').removeClass('flaticon-invisible');
			showPass = 0;
		}

	});
});
