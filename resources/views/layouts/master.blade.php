@inject('util','App\Classes\Util')
@inject("projectController", "App\Http\Controllers\ProjectController")

<!DOCTYPE html>
<html lang="{{ $urlLanguage['code'] ?? 'en' }}">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $tabTitle ?? Page::tabTitle() ?? Page::title() ?? config('app.name') }}</title>

	<meta name="theme-color" content="{{ $metaThemeColor ?? '' }}" />
	<meta name="author" content="{{ Page::author() ?? $metaAuthor ?? '' }}">
	<meta name="description" content="{{  Page::metaDescription() ?? $metaDescription ?? '' }}">

	<!-- CSS Files -->
	@yield('style')

	<!-- Fav -->
	@if(!empty(Brand::branding('brand-fav-icon')))
	<link rel="icon" href="{{ url('/storage') }}/{{ Brand::branding('brand-fav-icon') }}" type="image/png">
	@endif

	@if($forceNoIndexNoFollow ?? false)
	<meta name="robots" content="noindex, nofollow" />
	@elseif(env("APP_ENV") === "demo-1" && ($slug ?? NULL) === "home")
	<meta name="robots" content="index, follow" />
	@elseif(isset(Cache::get("settings")["noindex-nofollow"]) && Cache::get("settings")["noindex-nofollow"]["column_value"] == "1")
	<meta name="robots" content="noindex, nofollow" />
	@elseif(env("APP_ENV") !== "production")
	<meta name="robots" content="noindex, nofollow" />
	@endif

	{!! $util->generateSociaMetaTags($meta ?? null) !!}
	{!! $util->generateFAQSchemaJsonLd($meta["faqSchemaJsonLd"] ?? null) !!}

	@yield('head')
</head>

<body class="thin-scroll-bar" dir="{{ language()['direction'] ?? 'ltr' }}">

	@yield('content')

	<div class="overlay"></div>

	@livewireScripts

	<script>
		var DEBUG = false;
		var APP_NAME = '{{ config("app.name") }}';
		var APP_ENV = '{{ config("app.env") }}';
		var BASE_URL = '{{ config("app.url") }}';
		var THEME_URL = '{{ isset($themeURL) ? $themeURL("/") : config("app.url")  }}';
		var THEME_SLUG = '{{ themeSlug() ?? "" }}';
		var PORTAL_PREFIX = '{{config("app.portal_prefix")}}';
		var PREFIXED_URL = '{{ $util->prefixedURL("") }}';
		var PRIMARY_LANGUAGE_CODE = '{{ $primaryLanguage["code"] ?? "" }}';
		var URL_LANGUAGE_CODE = '{{ $urlLanguage["code"] ?? "" }}';
		var LANG_URL = '{{ langURL("/") }}';

		var SERVER_TZ = '{{ config("app.timezone") }}';
		var SERVER_DATE_FORMAT = 'YYYY/MM/DD';
		var SERVER_TIME_FORMAT = 'hh:mm:ss A';
		var SERVER_DATETIME_FORMAT = `${SERVER_DATE_FORMAT} ${SERVER_TIME_FORMAT}`;
		var CLIENT_DATE_FORMAT = 'DD/MM/YYYY';
		var CLIENT_TIME_FORMAT = 'hh:mm a';
		var CLIENT_DATETIME_FORMAT = `${CLIENT_DATE_FORMAT} ${CLIENT_TIME_FORMAT}`;
		var SAFE_USER = '{!! addSlashes(json_encode(request()["loggedInSafeUser"])) ?? "" !!}';
		var LAST_BACKEND_INTERACTION_SINCE = 0;
		var CLICK_INACTIVE_SINCE = 0;

		SAFE_USER = SAFE_USER !== "" ? JSON.parse(SAFE_USER) : null;

		var ROUTES = {
			completeProfile: 'complete-profile'
		};

		var FLASH_MESSAGE = '{!! addSlashes(json_encode(session("flashMessage"))) !!}';

		var CONFIRMATION_TEXTS = {
			delete: {
				title: '{{ __("delete-confirmation-title") }}',
				description: '{{ __("delete-confirmation-description") }}',
				positiveButtonText: '{{ __("delete-confirmation-positive-button-text") }}',
				negativeButtonText: '{{ __("delete-confirmation-negative-button-text") }}'
			}
		};

		var userRole = "{!! session('loggedInUser')['role_title'] ?? false !!}";

		let profileBadge = document.querySelector('.profile-badge');
		let inputHasSuggestions = document.querySelectorAll('.input-style-1.has-suggestions');

		// Defaults

		var DEFAULT_AVATAR = '{{ asset("assets/avatar.png") }}';
		var DEFAULT_IMAGE = '{{ asset("assets/default-image.jpg") }}';

		function logout() {
			if (SAFE_USER !== null) window.location.href = BASE_URL + '/' + PORTAL_PREFIX + '/logout';
		}

		// Triggers

		var Trigger = {
			OVERLAY_CLICK: null,
			PARAMS: null,
			clear: function() {
				this.OVERLAY_CLICK = null;
				this.PARAMS = null;
			}
		};


		// Global

		document.addEventListener('DOMContentLoaded', function() {
			showFlashMessage();
		});

		document.addEventListener('click', function(e) {
			let target = e.target;

			CLICK_INACTIVE_SINCE = 0;

			let closeNotificationPanel = target.closest('.main-shortcuts') === null;
			if (closeNotificationPanel === true) hideAllFloatingPanel();

			if (target.closest('.multi-select-dropdown') === null && typeof closeMultiSelectDropdown !== 'undefined') closeMultiSelectDropdown();
			if (target.closest('.profile-badge') === null && target.classList.contains('profile-badge') !== true && profileBadge !== null) profileBadge.classList.remove('active');
		});

		let preventDefaultElements = document.querySelectorAll('[data-prevent="default"]');

		preventDefaultElements.forEach((pdElement) => {
			pdElement.addEventListener('click', function() {
				event.preventDefault();
			});

			if (pdElement.nodeName === 'FORM') {
				pdElement.addEventListener('submit', function() {
					event.preventDefault();
				})
			}

		});


		// Notification Panel

		let notificationPanel = document.querySelector('.notification-panel');
		let toggleNotificationPanelElements = document.querySelectorAll('[data-is="toggle-notification-panel"]');

		toggleNotificationPanelElements.forEach(function(toggleNotificationPanelElement) {

			toggleNotificationPanelElement.addEventListener('click', function() {
				toggleClass(notificationPanel, 'active');
			});

		});


		// Profile Panel

		let profilePanel = document.querySelector('.profile-panel');
		let toggleProfilePanelElements = document.querySelectorAll('[data-is="toggle-profile-panel"]');

		toggleProfilePanelElements.forEach(function(toggleProfilePanelElement) {

			toggleProfilePanelElement.addEventListener('click', function() {
				toggleClass(profilePanel, 'active');
			});

		});

		function hideProfilePanel() {
			profilePanel.classList.remove('active');
		}

		function hideAllFloatingPanel() {
			let floatingPanels = document.querySelectorAll('.floating-panel');
			floatingPanels.forEach(function(floatingPanel) {
				floatingPanel.classList.remove('active');
			});
		}

		// Profile Badge

		if (profileBadge !== null) {
			profileBadge.addEventListener('click', function() {
				toggleClass(profileBadge, 'active');
			});
		}

		function toggleNavigation() {
			let primaryNavigationList = document.querySelector('.primary-navigation-list');
			toggleClass(primaryNavigationList, 'active');
		}

		// Fash Message

		function showFlashMessage() {

			if (!isEmpty(FLASH_MESSAGE) && FLASH_MESSAGE != 'null') {
				let message = JSON.parse(FLASH_MESSAGE);

				Notification.show({
					classes: [message.status],
					heading: message.heading,
					description: message.description,
				});
			}
		}


		// clock

		function initTicks() {
			setInterval(() => {
				LAST_BACKEND_INTERACTION_SINCE++;
				CLICK_INACTIVE_SINCE++;
				lastBackendInteractionLimitReached();
			}, 1000);
		}

		function lastBackendInteractionLimitReached(limit = 3600) {
			//unit=seconds
			if (CLICK_INACTIVE_SINCE >= limit && logout !== undefined) logout();
		}

		initTicks();

		// helper

		function countryList() {
			return [{
					"name": "Andorra",
					"code": "AD"
				},
				{
					"name": "United Arab Emirates",
					"code": "AE"
				},
				{
					"name": "Afghanistan",
					"code": "AF"
				},
				{
					"name": "Antigua and Barbuda",
					"code": "AG"
				},
				{
					"name": "Anguilla",
					"code": "AI"
				},
				{
					"name": "Albania",
					"code": "AL"
				},
				{
					"name": "Armenia",
					"code": "AM"
				},
				{
					"name": "Netherlands Antilles",
					"code": "AN"
				},
				{
					"name": "Angola",
					"code": "AO"
				},
				{
					"name": "Antarctica",
					"code": "AQ"
				},
				{
					"name": "Argentina",
					"code": "AR"
				},
				{
					"name": "American Samoa",
					"code": "AS"
				},
				{
					"name": "Austria",
					"code": "AT"
				},
				{
					"name": "Australia",
					"code": "AU"
				},
				{
					"name": "Aruba",
					"code": "AW"
				},
				{
					"name": "Azerbaijan",
					"code": "AZ"
				},
				{
					"name": "Bosnia and Herzegovina",
					"code": "BA"
				},
				{
					"name": "Barbados",
					"code": "BB"
				},
				{
					"name": "Bangladesh",
					"code": "BD"
				},
				{
					"name": "Belgium",
					"code": "BE"
				},
				{
					"name": "Burkina Faso",
					"code": "BF"
				},
				{
					"name": "Bulgaria",
					"code": "BG"
				},
				{
					"name": "Bahrain",
					"code": "BH"
				},
				{
					"name": "Burundi",
					"code": "BI"
				},
				{
					"name": "Benin",
					"code": "BJ"
				},
				{
					"name": "Bermuda",
					"code": "BM"
				},
				{
					"name": "Brunei Darussalam",
					"code": "BN"
				},
				{
					"name": "Bolivia",
					"code": "BO"
				},
				{
					"name": "Brazil",
					"code": "BR"
				},
				{
					"name": "Bahamas",
					"code": "BS"
				},
				{
					"name": "Bhutan",
					"code": "BT"
				},
				{
					"name": "Bouvet Island",
					"code": "BV"
				},
				{
					"name": "Botswana",
					"code": "BW"
				},
				{
					"name": "Belarus",
					"code": "BY"
				},
				{
					"name": "Belize",
					"code": "BZ"
				},
				{
					"name": "Canada",
					"code": "CA"
				},
				{
					"name": "Cocos (Keeling) Islands",
					"code": "CC"
				},
				{
					"name": "Congo, The Democratic Republic of the",
					"code": "CD"
				},
				{
					"name": "Central African Republic",
					"code": "CF"
				},
				{
					"name": "Congo",
					"code": "CG"
				},
				{
					"name": "Switzerland",
					"code": "CH"
				},
				{
					"name": "Cote d'Ivoire",
					"code": "CI"
				},
				{
					"name": "Cook Islands",
					"code": "CK"
				},
				{
					"name": "Chile",
					"code": "CL"
				},
				{
					"name": "Cameroon",
					"code": "CM"
				},
				{
					"name": "China",
					"code": "CN"
				},
				{
					"name": "Colombia",
					"code": "CO"
				},
				{
					"name": "Costa Rica",
					"code": "CR"
				},
				{
					"name": "Cuba",
					"code": "CU"
				},
				{
					"name": "Cape Verde",
					"code": "CV"
				},
				{
					"name": "Christmas Island",
					"code": "CX"
				},
				{
					"name": "Cyprus",
					"code": "CY"
				},
				{
					"name": "Czech Republic",
					"code": "CZ"
				},
				{
					"name": "Germany",
					"code": "DE"
				},
				{
					"name": "Djibouti",
					"code": "DJ"
				},
				{
					"name": "Denmark",
					"code": "DK"
				},
				{
					"name": "Dominica",
					"code": "DM"
				},
				{
					"name": "Dominican Republic",
					"code": "DO"
				},
				{
					"name": "Algeria",
					"code": "DZ"
				},
				{
					"name": "Ecuador",
					"code": "EC"
				},
				{
					"name": "Estonia",
					"code": "EE"
				},
				{
					"name": "Egypt",
					"code": "EG"
				},
				{
					"name": "Western Sahara",
					"code": "EH"
				},
				{
					"name": "Eritrea",
					"code": "ER"
				},
				{
					"name": "Spain",
					"code": "ES"
				},
				{
					"name": "Ethiopia",
					"code": "ET"
				},
				{
					"name": "Finland",
					"code": "FI"
				},
				{
					"name": "Fiji",
					"code": "FJ"
				},
				{
					"name": "Falkland Islands (Malvinas)",
					"code": "FK"
				},
				{
					"name": "Micronesia (Federated States of)",
					"code": "FM"
				},
				{
					"name": "Faroe Islands",
					"code": "FO"
				},
				{
					"name": "France",
					"code": "FR"
				},
				{
					"name": "Obsolete see FR territory",
					"code": "FX"
				},
				{
					"name": "Gabon",
					"code": "GA"
				},
				{
					"name": "England",
					"code": "GB"
				},
				{
					"name": "United Kingdom",
					"code": "GB"
				},
				{
					"name": "Grenada",
					"code": "GD"
				},
				{
					"name": "Georgia",
					"code": "GE"
				},
				{
					"name": "French Guiana",
					"code": "GF"
				},
				{
					"name": "Ghana",
					"code": "GH"
				},
				{
					"name": "Gibraltar",
					"code": "GI"
				},
				{
					"name": "Greenland",
					"code": "GL"
				},
				{
					"name": "Gambia",
					"code": "GM"
				},
				{
					"name": "Guinea",
					"code": "GN"
				},
				{
					"name": "Guadeloupe",
					"code": "GP"
				},
				{
					"name": "Equatorial Guinea",
					"code": "GQ"
				},
				{
					"name": "Greece",
					"code": "GR"
				},
				{
					"name": "South Georgia and the South Sandwich Islands",
					"code": "GS"
				},
				{
					"name": "Guatemala",
					"code": "GT"
				},
				{
					"name": "Guam",
					"code": "GU"
				},
				{
					"name": "Guinea-Bissau",
					"code": "GW"
				},
				{
					"name": "Guyana",
					"code": "GY"
				},
				{
					"name": "Hong Kong",
					"code": "HK"
				},
				{
					"name": "Heard Island and McDonald Islands",
					"code": "HM"
				},
				{
					"name": "Honduras",
					"code": "HN"
				},
				{
					"name": "Croatia",
					"code": "HR"
				},
				{
					"name": "Haiti",
					"code": "HT"
				},
				{
					"name": "Hungary",
					"code": "HU"
				},
				{
					"name": "Indonesia",
					"code": "ID"
				},
				{
					"name": "Ireland",
					"code": "IE"
				},
				{
					"name": "Israel",
					"code": "IL"
				},
				{
					"name": "India",
					"code": "IN"
				},
				{
					"name": "British Indian Ocean Territory",
					"code": "IO"
				},
				{
					"name": "Iraq",
					"code": "IQ"
				},
				{
					"name": "Iran (Islamic Republic of)",
					"code": "IR"
				},
				{
					"name": "Iceland",
					"code": "IS"
				},
				{
					"name": "Italy",
					"code": "IT"
				},
				{
					"name": "Jamaica",
					"code": "JM"
				},
				{
					"name": "Jordan",
					"code": "JO"
				},
				{
					"name": "Japan",
					"code": "JP"
				},
				{
					"name": "Kenya",
					"code": "KE"
				},
				{
					"name": "Kyrgyzstan",
					"code": "KG"
				},
				{
					"name": "Cambodia",
					"code": "KH"
				},
				{
					"name": "Kiribati",
					"code": "KI"
				},
				{
					"name": "Comoros",
					"code": "KM"
				},
				{
					"name": "Saint Kitts and Nevis",
					"code": "KN"
				},
				{
					"name": "Korea, Democratic People's Republic of",
					"code": "KP"
				},
				{
					"name": "Korea, Republic of",
					"code": "KR"
				},
				{
					"name": "Kuwait",
					"code": "KW"
				},
				{
					"name": "Cayman Islands",
					"code": "KY"
				},
				{
					"name": "Kazakhstan",
					"code": "KZ"
				},
				{
					"name": "Lao People's Democratic Republic",
					"code": "LA"
				},
				{
					"name": "Lebanon",
					"code": "LB"
				},
				{
					"name": "Saint Lucia",
					"code": "LC"
				},
				{
					"name": "Liechtenstein",
					"code": "LI"
				},
				{
					"name": "Sri Lanka",
					"code": "LK"
				},
				{
					"name": "Liberia",
					"code": "LR"
				},
				{
					"name": "Lesotho",
					"code": "LS"
				},
				{
					"name": "Lithuania",
					"code": "LT"
				},
				{
					"name": "Luxembourg",
					"code": "LU"
				},
				{
					"name": "Latvia",
					"code": "LV"
				},
				{
					"name": "Obsolete see LT territory",
					"code": "LX"
				},
				{
					"name": "Libyan Arab Jamahiriya",
					"code": "LY"
				},
				{
					"name": "Morocco",
					"code": "MA"
				},
				{
					"name": "Monaco",
					"code": "MC"
				},
				{
					"name": "Moldova, Republic of",
					"code": "MD"
				},
				{
					"name": "Madagascar",
					"code": "MG"
				},
				{
					"name": "Marshall Islands",
					"code": "MH"
				},
				{
					"name": "Macedonia, The Former Yugoslav Republic of",
					"code": "MK"
				},
				{
					"name": "Myanmar",
					"code": "MM"
				},
				{
					"name": "Mongolia",
					"code": "MN"
				},
				{
					"name": "Macau",
					"code": "MO"
				},
				{
					"name": "Northern Mariana Islands",
					"code": "MP"
				},
				{
					"name": "Martinique",
					"code": "MQ"
				},
				{
					"name": "Mauritania",
					"code": "MR"
				},
				{
					"name": "Montserrat",
					"code": "MS"
				},
				{
					"name": "Malta",
					"code": "MT"
				},
				{
					"name": "Mauritius",
					"code": "MU"
				},
				{
					"name": "Maldives",
					"code": "MV"
				},
				{
					"name": "Malawi",
					"code": "MW"
				},
				{
					"name": "Mexico",
					"code": "MX"
				},
				{
					"name": "Malaysia",
					"code": "MY"
				},
				{
					"name": "Mozambique",
					"code": "MZ"
				},
				{
					"name": "Namibia",
					"code": "NA"
				},
				{
					"name": "New Caledonia",
					"code": "NC"
				},
				{
					"name": "Niger",
					"code": "NE"
				},
				{
					"name": "Norfolk Island",
					"code": "NF"
				},
				{
					"name": "Nigeria",
					"code": "NG"
				},
				{
					"name": "Nicaragua",
					"code": "NI"
				},
				{
					"name": "Netherlands",
					"code": "NL"
				},
				{
					"name": "Norway",
					"code": "NO"
				},
				{
					"name": "Nepal",
					"code": "NP"
				},
				{
					"name": "Nauru",
					"code": "NR"
				},
				{
					"name": "Niue",
					"code": "NU"
				},
				{
					"name": "New Zealand",
					"code": "NZ"
				},
				{
					"name": "Oman",
					"code": "OM"
				},
				{
					"name": "Panama",
					"code": "PA"
				},
				{
					"name": "Peru",
					"code": "PE"
				},
				{
					"name": "French Polynesia",
					"code": "PF"
				},
				{
					"name": "Papua New Guinea",
					"code": "PG"
				},
				{
					"name": "Philippines",
					"code": "PH"
				},
				{
					"name": "Pakistan",
					"code": "PK"
				},
				{
					"name": "Poland",
					"code": "PL"
				},
				{
					"name": "Saint Pierre and Miquelon",
					"code": "PM"
				},
				{
					"name": "Pitcairn",
					"code": "PN"
				},
				{
					"name": "Puerto Rico",
					"code": "PR"
				},
				{
					"name": "Palestinian Territory, Occupied",
					"code": "PS"
				},
				{
					"name": "Portugal",
					"code": "PT"
				},
				{
					"name": "Palau",
					"code": "PW"
				},
				{
					"name": "Paraguay",
					"code": "PY"
				},
				{
					"name": "Qatar",
					"code": "QA"
				},
				{
					"name": "Reunion",
					"code": "RE"
				},
				{
					"name": "Romania",
					"code": "RO"
				},
				{
					"name": "Russian Federation",
					"code": "RU"
				},
				{
					"name": "Rwanda",
					"code": "RW"
				},
				{
					"name": "Saudi Arabia",
					"code": "SA"
				},
				{
					"name": "Solomon Islands",
					"code": "SB"
				},
				{
					"name": "Seychelles",
					"code": "SC"
				},
				{
					"name": "Sudan",
					"code": "SD"
				},
				{
					"name": "Sweden",
					"code": "SE"
				},
				{
					"name": "Singapore",
					"code": "SG"
				},
				{
					"name": "Saint Helena",
					"code": "SH"
				},
				{
					"name": "Slovenia",
					"code": "SI"
				},
				{
					"name": "Svalbard and Jan Mayen Islands",
					"code": "SJ"
				},
				{
					"name": "Slovakia",
					"code": "SK"
				},
				{
					"name": "Sierra Leone",
					"code": "SL"
				},
				{
					"name": "San Marino",
					"code": "SM"
				},
				{
					"name": "Senegal",
					"code": "SN"
				},
				{
					"name": "Somalia",
					"code": "SO"
				},
				{
					"name": "Suriname",
					"code": "SR"
				},
				{
					"name": "Sao Tome and Principe",
					"code": "ST"
				},
				{
					"name": "El Salvador",
					"code": "SV"
				},
				{
					"name": "Syrian Arab Republic",
					"code": "SY"
				},
				{
					"name": "Swaziland",
					"code": "SZ"
				},
				{
					"name": "Turks and Caicos Islands",
					"code": "TC"
				},
				{
					"name": "Chad",
					"code": "TD"
				},
				{
					"name": "French Southern Territories",
					"code": "TF"
				},
				{
					"name": "Togo",
					"code": "TG"
				},
				{
					"name": "Thailand",
					"code": "TH"
				},
				{
					"name": "Tajikistan",
					"code": "TJ"
				},
				{
					"name": "Tokelau",
					"code": "TK"
				},
				{
					"name": "Turkmenistan",
					"code": "TM"
				},
				{
					"name": "Tunisia",
					"code": "TN"
				},
				{
					"name": "Tonga",
					"code": "TO"
				},
				{
					"name": "East Timor",
					"code": "TP"
				},
				{
					"name": "Turkey",
					"code": "TR"
				},
				{
					"name": "Trinidad and Tobago",
					"code": "TT"
				},
				{
					"name": "Tuvalu",
					"code": "TV"
				},
				{
					"name": "Taiwan",
					"code": "TW"
				},
				{
					"name": "Tanzania, United Republic of",
					"code": "TZ"
				},
				{
					"name": "Ukraine",
					"code": "UA"
				},
				{
					"name": "Uganda",
					"code": "UG"
				},
				{
					"name": "United States Minor Outlying Islands",
					"code": "UM"
				},
				{
					"name": "United States",
					"code": "US"
				},
				{
					"name": "Uruguay",
					"code": "UY"
				},
				{
					"name": "Uzbekistan",
					"code": "UZ"
				},
				{
					"name": "Holy See (Vatican City State)",
					"code": "VA"
				},
				{
					"name": "Saint Vincent and the Grenadines",
					"code": "VC"
				},
				{
					"name": "Venezuela",
					"code": "VE"
				},
				{
					"name": "Virgin Islands, British",
					"code": "VG"
				},
				{
					"name": "Virgin Islands, U.S.",
					"code": "VI"
				},
				{
					"name": "Viet Nam",
					"code": "VN"
				},
				{
					"name": "Vanuatu",
					"code": "VU"
				},
				{
					"name": "Wallis and Futuna",
					"code": "WF"
				},
				{
					"name": "Samoa",
					"code": "WS"
				},
				{
					"name": "Yemen",
					"code": "YE"
				},
				{
					"name": "Mayotte",
					"code": "YT"
				},
				{
					"name": "Yugoslavia",
					"code": "YU"
				},
				{
					"name": "South Africa",
					"code": "ZA"
				},
				{
					"name": "Zambia",
					"code": "ZM"
				},
				{
					"name": "Unknown",
					"code": "ZR"
				},
				{
					"name": "Zimbabwe",
					"code": "ZW"
				}
			];
		}

		function generateCountriesOptions(countryCode = null) {

			let countriesView = countryList().map(country => {
				return `<option ${countryCode === country.code ? 'selected' : ''} value="${country.code}">${country.name}</option>`;
			});

			return countriesView.join('');
		}

		function newPassword() {
			const length = 10;
			const upperCaseLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			const lowerCaseLetters = 'abcdefghijklmnopqrstuvwxyz';
			const numbers = '0123456789';
			const allChars = upperCaseLetters + lowerCaseLetters + numbers;

			let password = '';
			let hasUpperCase = false;
			let hasLowerCase = false;
			let hasNumber = false;

			while (password.length < length || !(hasUpperCase && hasLowerCase && hasNumber)) {
				const randomChar = allChars[Math.floor(Math.random() * allChars.length)];
				password += randomChar;

				if (upperCaseLetters.includes(randomChar)) {
					hasUpperCase = true;
				} else if (lowerCaseLetters.includes(randomChar)) {
					hasLowerCase = true;
				} else if (numbers.includes(randomChar)) {
					hasNumber = true;
				}
			}

			return password;
		}

		function generatePassword() {
			let target = event.target;
			let container = target.closest('div');
			container.querySelector('input').value = newPassword();
		}
	</script>

	@php
	echo $projectController->loadFile("resources/js/private/util.min.js");
	echo $projectController->loadFile("resources/js/private/date-time.min.js");
	echo $projectController->loadFile("resources/js/private/event.min.js");
	echo $projectController->loadFile("resources/js/private/xhr.min.js");
	echo $projectController->loadFile("resources/js/private/data-table.min.js");
	echo $projectController->loadFile("resources/js/private/confirmation.min.js");
	echo $projectController->loadFile("resources/js/private/notification.min.js");
	echo $projectController->loadFile("resources/js/private/eloader.min.js");
	echo $projectController->loadFile("resources/js/private/overlay.min.js");
	@endphp

	@yield('script')
	@yield('content-bottom')
	@yield('foot')
</body>

</html>