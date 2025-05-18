<?php

use App\Classes\DateTime;
use App\Http\Controllers\ThemeController;
use App\Plugins\MediaCenter\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

return new class extends Seeder
{
	public function run()
	{
		$abilities = [
			[
				"ability" => "view-components",
				"role_title" => "admin"
			],
			[
				"ability" => "add-components",
				"role_title" => "admin"
			],
			[
				"ability" => "update-components",
				"role_title" => "admin"
			],
			[
				"ability" => "delete-components",
				"role_title" => "admin"
			]
		];

		DB::table("abilities")->insert($abilities);

		$themeController = new ThemeController();
		$activeTheme = $themeController->getActiveTheme();

		// TODO: make dynamic
		if ($activeTheme !== NULL && $activeTheme["slug"] === "luxe-landing") {

			// 1
			$heroImage1 = Media::addMedia([
				"url" => "temp/luxe-landing/hero-img-1.png",
				"type" => "image/png",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);

			$heroImage2 = Media::addMedia([
				"url" => "temp/luxe-landing/hero-img-2.png",
				"type" => "image/png",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);

			$heroComponentId = DB::table("components")->insertGetId([
				"title" => "Hero Section",
				"slug" => "hero-section",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Announcement Text",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Title",
							"type" => "string",
							"slug" => "title"
						],
						[
							"label" => "Description",
							"type" => "text",
							"slug" => "description"
						],
						[
							"label" => "Image 1",
							"type" => "image",
							"slug" => "image-1",
							"instructions" => ["Recommended image size: 150 x 160 (pixels)"]
						],
						[
							"label" => "Image 2",
							"type" => "image",
							"slug" => "image-2",
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "title",
					"column_value" => "Your trusted hands-off management partner",
					"component_id" => $heroComponentId
				],
				[
					"column_name" => "description",
					"column_value" => "We provide round-the-clock check-in times; fully managed cleaning, linen and maintenance service; and intricate management of listings on all leading platforms.",
					"component_id" => $heroComponentId
				],
				[
					"column_name" => "image-1",
					"column_value" => $heroImage1->id,
					"component_id" => $heroComponentId
				],
				[
					"column_name" => "image-2",
					"column_value" => $heroImage2->id,
					"component_id" => $heroComponentId
				],
			]);

			// 2
			$logo1 = Media::addMedia([
				"url" => "temp/luxe-landing/airbnb-logo.png",
				"type" => "image/png",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);
			$logo2 = Media::addMedia([
				"url" => "temp/luxe-landing/vrbo-logo.png",
				"type" => "image/png",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);
			$logo3 = Media::addMedia([
				"url" => "temp/luxe-landing/booking-logo.png",
				"type" => "image/png",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);

			$affiliatesComponentId = DB::table("components")->insertGetId([
				"title" => "Affiliate Logos",
				"slug" => "affiliate-logos",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Logo",
					"maxEntities" => NULL,
					"fields" => [
						[
							"label" => "Image",
							"type" => "image",
							"slug" => "image",
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "image",
					"column_value" => $logo1->id,
					"component_id" => $affiliatesComponentId
				],
				[
					"column_name" => "image",
					"column_value" => $logo2->id,
					"component_id" => $affiliatesComponentId
				],
				[
					"column_name" => "image",
					"column_value" => $logo3->id,
					"component_id" => $affiliatesComponentId
				]
			]);

			// 3

			$featuresComponentId = DB::table("components")->insertGetId([
				"title" => "Features",
				"slug" => "features",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Feature",
					"maxEntities" => NULL,
					"fields" => [
						[
							"label" => "Icon",
							"type" => "string",
							"slug" => "icon",
						],
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading",
						],
						[
							"label" => "Description",
							"type" => "text",
							"slug" => "description",
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "icon",
					"column_value" => "home-icon",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "heading",
					"column_value" => "Optimized Properties",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "description",
					"column_value" => "Passionate about beautiful spaces, all our properties are carefully curated to ensure an exquisite selection of properties for discerning clients and owners alike.",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "icon",
					"column_value" => "flexible-icon",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "heading",
					"column_value" => "Flexible",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "description",
					"column_value" => "Always owner first. You rent your home on your terms with complete control through our owners portal.",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "icon",
					"column_value" => "check-in-icon",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "heading",
					"column_value" => "24/7 Check-in",
					"component_id" => $featuresComponentId
				],
				[
					"column_name" => "description",
					"column_value" => "Short to Medium-term rental can be more than twice as profitable as long-term renting. We use our experience to manage pricing to maximise your profits. ",
					"component_id" => $featuresComponentId
				],
			]);

			// 4

			$experienceImg1 = Media::addMedia([
				"url" => "temp/luxe-landing/guest-experience-1.jpg",
				"type" => "image/jpg",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);

			$experienceImg2 = Media::addMedia([
				"url" => "temp/luxe-landing/guest-experience-2.jpg",
				"type" => "image/jpg",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);

			$experienceId = DB::table("components")->insertGetId([
				"title" => "Guest Experience",
				"slug" => "guest-experience",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Entity",
					"maxEntities" => NULL,
					"fields" => [
						[
							"label" => "Icon 1",
							"type" => "string",
							"slug" => "icon-1",
						],
						[
							"label" => "Heading 1",
							"type" => "string",
							"slug" => "heading-1",
						],
						[
							"label" => "Text 1",
							"type" => "text",
							"slug" => "text-1",
						],
						[
							"label" => "Icon 2",
							"type" => "string",
							"slug" => "icon-2",
						],
						[
							"label" => "Heading 2",
							"type" => "string",
							"slug" => "heading-2",
						],
						[
							"label" => "Text 2",
							"type" => "text",
							"slug" => "text-2",
						],
						[
							"label" => "Icon 3",
							"type" => "string",
							"slug" => "icon-3",
						],
						[
							"label" => "Heading 3",
							"type" => "string",
							"slug" => "heading-3",
						],
						[
							"label" => "Text 3",
							"type" => "text",
							"slug" => "text-3",
						],
						[
							"label" => "Image",
							"type" => "image",
							"slug" => "image",
						],
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "icon-1",
					"column_value" => "property-photos-icon",
					"component_id" => $experienceId
				],
				[
					"column_name" => "heading-1",
					"column_value" => "Property photos and listing creation",
					"component_id" => $experienceId
				],
				[
					"column_name" => "text-1",
					"column_value" => "During onboarding We arrange for professional photos to ensure your property stands out from the crowd on all the leading platforms.",
					"component_id" => $experienceId
				],
				[
					"column_name" => "icon-2",
					"column_value" => "check-in-alt-icon",
					"component_id" => $experienceId
				],
				[
					"column_name" => "heading-2",
					"column_value" => "24/7 Check-in",
					"component_id" => $experienceId
				],
				[
					"column_name" => "text-2",
					"column_value" => "Your guests will always be warmly welcomed and provided with personalised information about their stay and the local area.",
					"component_id" => $experienceId
				],
				[
					"column_name" => "icon-3",
					"column_value" => "home-icon",
					"component_id" => $experienceId
				],
				[
					"column_name" => "heading-3",
					"column_value" => "Property Inspections",
					"component_id" => $experienceId
				],
				[
					"column_name" => "text-3",
					"column_value" => "After each guest stay we will inspect your property to ensure it remains in tip-top condition.",
					"component_id" => $experienceId
				],
				[
					"column_name" => "image",
					"column_value" => $experienceImg1->id,
					"component_id" => $experienceId
				],
				[
					"column_name" => "icon-1",
					"column_value" => "hand-icon",
					"component_id" => $experienceId
				],
				[
					"column_name" => "heading-1",
					"column_value" => "Professional cleaning & quality linens",
					"component_id" => $experienceId
				],
				[
					"column_name" => "text-1",
					"column_value" => "Our team of cleaners will ensure your property is always spotless, provide high quality linens, and refill kitchen/ bathroom amenities on request.s",
					"component_id" => $experienceId
				],
				[
					"column_name" => "icon-2",
					"column_value" => "flexible-icon",
					"component_id" => $experienceId
				],
				[
					"column_name" => "heading-2",
					"column_value" => "Dedicated Owner Communication",
					"component_id" => $experienceId
				],
				[
					"column_name" => "text-2",
					"column_value" => "Through our Owners portal and your personal property manager, you'll be able to review performance, pricing, occupancy, or book your own stays at any time.",
					"component_id" => $experienceId
				],
				[
					"column_name" => "icon-3",
					"column_value" => "repair-icon",
					"component_id" => $experienceId
				],
				[
					"column_name" => "heading-3",
					"column_value" => "Repairs and Maintenance",
					"component_id" => $experienceId
				],
				[
					"column_name" => "text-3",
					"column_value" => "Our team is on call 24/7 to respond to any repair and maintenance issue, no matter how big or small.",
					"component_id" => $experienceId
				],
				[
					"column_name" => "image",
					"column_value" => $experienceImg2->id,
					"component_id" => $experienceId
				],

			]);

			// 5

			$ctaId = DB::table("components")->insertGetId([
				"title" => "Call To Action",
				"slug" => "cta-1",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "CTA",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading",
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "Ready to maximize your rental income?",
					"component_id" => $ctaId
				]
			]);

			// 6
			$easyStepsId = DB::table("components")->insertGetId([
				"title" => "Easy Steps",
				"slug" => "easy-steps",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Step",
					"maxEntities" => NULL,
					"fields" => [
						[
							"label" => "Icon",
							"type" => "string",
							"slug" => "icon",
						],
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading",
						],
						[
							"label" => "Description",
							"type" => "text",
							"slug" => "description",
						]

					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "icon",
					"column_value" => "handshake-icon",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "heading",
					"column_value" => "Contact the Team",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "description",
					"column_value" => "Please fill out the typeform and one of our dedicated team 
					members will contact you to discuss your needs and learn 
					more about the property.",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "icon",
					"column_value" => "property-photos-icon",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "heading",
					"column_value" => "Property Onboarding",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "description",
					"column_value" => "We will schedule a viewing of the property, allowing us to 
					properly highlight your home. A great opportunity to meet 
					and get a feel for how you want your property managed.",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "icon",
					"column_value" => "relax-icon",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "heading",
					"column_value" => "Sit back and relax…",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "description",
					"column_value" => "With our owner's portal you can review analytics and performance of your property with the click of a button!",
					"component_id" => $easyStepsId
				],
			]);

			// 7

			$aboutImg = Media::addMedia([
				"url" => "temp/luxe-landing/about.jpg",
				"type" => "image/jpg",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);

			$easyStepsId = DB::table("components")->insertGetId([
				"title" => "About us",
				"slug" => "about-us",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Entity",
					"maxEntities" => NULL,
					"fields" => [
						[
							"label" => "Description",
							"type" => "text",
							"slug" => "description",
						],
						[
							"label" => "Image",
							"type" => "image",
							"slug" => "image",
						],
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "description",
					"column_value" => "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Obcaecati porro vitae praesentium reprehenderit beatae debitis, illo rerum minus eveniet voluptate similique incidunt error eum voluptatem a. Eos quis odit veniam.\n\nLorem ipsum dolor sit amet consectetur, adipisicing elit. Obcaecati porro vitae praesentium reprehenderit beatae debitis, illo rerum minus eveniet voluptate similique incidunt error eum voluptatem a. Eos quis odit veniam.",
					"component_id" => $easyStepsId
				],
				[
					"column_name" => "image",
					"column_value" => $aboutImg->id,
					"component_id" => $easyStepsId
				]
			]);

			// 8

			$list = '
					<ul class="list | margin-top-2">
					<li>
						<svg class="icon">
							<use xlink:href="http://localhost:8001/themes/luxe-landing/assets/icons.svg#outline-check-mark"></use>
						</svg>
						<span>Choose the length of your commitment (min 3 months)</span>
					</li>
					<li>
						<svg class="icon">
							<use xlink:href="http://localhost:8001/themes/luxe-landing/assets/icons.svg#outline-check-mark"></use>
						</svg>
						<span>24/7 Guest service and management on all leading platforms</span>
					</li>
					<li>
						<svg class="icon">
							<use xlink:href="http://localhost:8001/themes/luxe-landing/assets/icons.svg#outline-check-mark"></use>
						</svg>
						<span>Algorithm-driven price optimisation.</span>
					</li>
					<li>
						<svg class="icon">
							<use xlink:href="http://localhost:8001/themes/luxe-landing/assets/icons.svg#outline-check-mark"></use>
						</svg>
						<span>Bespoke Owner\'s dashboard.</span>
					</li>
				</ul>
			';

			$plansId = DB::table("components")->insertGetId([
				"title" => "Plans",
				"slug" => "plans",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Plan",
					"maxEntities" => NULL,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading",
						],
						[
							"label" => "Sub Heading",
							"type" => "string",
							"slug" => "sub-heading",
						],
						[
							"label" => "Background Color",
							"type" => "string",
							"slug" => "bg-color",
						],
						[
							"label" => "List",
							"type" => "text",
							"slug" => "list",
						],
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "Fully Flexible",
					"component_id" => $plansId
				],
				[
					"column_name" => "sub-heading",
					"column_value" => "From 12% (+VAT)",
					"component_id" => $plansId
				],
				[
					"column_name" => "bg-color",
					"column_value" => "white",
					"component_id" => $plansId
				],
				[
					"column_name" => "list",
					"column_value" => $list,
					"component_id" => $plansId
				],

				[
					"column_name" => "heading",
					"column_value" => "12 Months",
					"component_id" => $plansId
				],
				[
					"column_name" => "sub-heading",
					"column_value" => "From 10% (+VAT)",
					"component_id" => $plansId
				],
				[
					"column_name" => "bg-color",
					"column_value" => "grey",
					"component_id" => $plansId
				],
				[
					"column_name" => "list",
					"column_value" => $list,
					"component_id" => $plansId
				],
				
				[
					"column_name" => "heading",
					"column_value" => "Premium",
					"component_id" => $plansId
				],
				[
					"column_name" => "sub-heading",
					"column_value" => "Extra 2% (+VAT)",
					"component_id" => $plansId
				],
				[
					"column_name" => "bg-color",
					"column_value" => "secondary-400",
					"component_id" => $plansId
				],
				[
					"column_name" => "list",
					"column_value" => $list,
					"component_id" => $plansId
				],

			]);

			// 9

			$contactId = DB::table("components")->insertGetId([
				"title" => "Contact Information",
				"slug" => "contact-info",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Info",
					"maxEntities" => NULL,
					"fields" => [
						[
							"label" => "icon",
							"type" => "string",
							"slug" => "icon",
						],
						[
							"label" => "Value",
							"type" => "string",
							"slug" => "value",
						],
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "icon",
					"column_value" => "solid-phone",
					"component_id" => $contactId
				],
				[
					"column_name" => "value",
					"column_value" => "12345678",
					"component_id" => $contactId
				],
				[
					"column_name" => "icon",
					"column_value" => "solid-envelope",
					"component_id" => $contactId
				],
				[
					"column_name" => "value",
					"column_value" => "hello@oliveandlulu.com",
					"component_id" => $contactId
				],
				

			]);

			// 10

			$footerCopyrightsId = DB::table("components")->insertGetId([
				"title" => "Footer Copyrights",
				"slug" => "footer-copyrights",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Text",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Text",
							"type" => "string",
							"slug" => "text",
						],
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "text",
					"column_value" => "Olive & Lulu © Copyright 2023",
					"component_id" => $footerCopyrightsId
				]
			]);

			// 11

			$contactPageSubheadingId = DB::table("components")->insertGetId([
				"title" => "Get Started Sub-Heading",
				"slug" => "get-started-sub-heading",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "Text",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Text",
							"type" => "string",
							"slug" => "text",
						],
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "text",
					"column_value" => "Tell us more about your home",
					"component_id" => $contactPageSubheadingId
				]
			]);
		}

		if($activeTheme !== NULL && $activeTheme["slug"] == "dj-feed"){
			
			// 1
			$heroSlider = Media::addMedia([
				"url" => "temp/dj-feed/hero.jpg",
				"type" => "image/jpg",
				"options" => NULL,
				"user_id" => 1,
				"folder_id" => 1
			]);

			$heroComponentId = DB::table("components")->insertGetId([
				"title" => "Hero Section",
				"slug" => "hero-section",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "section",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading"
						],
						[
							"label" => "Sub-Heading",
							"type" => "string",
							"slug" => "sub-heading"
						],
						[
							"label" => "Description",
							"type" => "string",
							"slug" => "description"
						],
						[
							"label" => "Button Text",
							"type" => "string",
							"slug" => "button-text"
						],
						[
							"label" => "Image",
							"type" => "image",
							"slug" => "image"
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "<span class='block'>The Ultimate</span> Event Feed System",
					"component_id" => $heroComponentId
				],
				[
					"column_name" => "sub-heading",
					"column_value" => "EXPERIENCE",
					"component_id" => $heroComponentId
				],
				[
					"column_name" => "description",
					"column_value" => "Where music, dancing and community come together!",
					"component_id" => $heroComponentId
				],
				[
					"column_name" => "button-text",
					"column_value" => "Get Started!",
					"component_id" => $heroComponentId
				],
				[
					"column_name" => "image",
					"column_value" => $heroSlider->id,
					"component_id" => $heroComponentId
				]
			]);

			// 2

			$aboutComponentId = DB::table("components")->insertGetId([
				"title" => "About",
				"slug" => "about",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "section",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading"
						],
						[
							"label" => "Button Text",
							"type" => "string",
							"slug" => "button-text"
						],
						[
							"label" => "Description",
							"type" => "text",
							"slug" => "description"
						],
						
						
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "ABOUT SYSTEM",
					"component_id" => $aboutComponentId
				],
				[
					"column_name" => "description",
					"column_value" => "At ELD Feed, we are dedicated to enhancing your event experiences. Our event feed system is designed to provide you with the latest and most relevant information about upcoming events, ensuring that you stay connected with the activities that matter most to you.\n\nOur system is not just another generic platform; it's a dynamic and user-centric solution tailored to your needs. We offer personalized event recommendations, real-time updates, and a user-friendly interface. Whether it's concerts, sports events, cultural festivals, or community gatherings, you'll receive instant notifications so you never miss out on what's happening.\n\nJoin our vibrant community of event enthusiasts and discover a world of possibilities. Thank you for choosing ELD Feed for all your event needs. We look forward to sharing countless unforgettable moments with you.",
					"component_id" => $aboutComponentId
				],
				[
					"column_name" => "button-text",
					"column_value" => "GET IN TOUCH",
					"component_id" => $aboutComponentId
				]
			]);

			// 3

			$featureSectionId = DB::table("components")->insertGetId([
				"title" => "Feature Section",
				"slug" => "feature-section",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "section",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading"
						],
						[
							"label" => "Sub-Heading",
							"type" => "string",
							"slug" => "sub-heading"
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "<span class='block'>Packed With</span> Exciting Features",
					"component_id" => $featureSectionId
				],
				[
					"column_name" => "sub-heading",
					"column_value" => "FEATURES",
					"component_id" => $featureSectionId
				]
			]);

			// 4

			$featuresId = DB::table("components")->insertGetId([
				"title" => "Features",
				"slug" => "features",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "feature",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading"
						],
						[
							"label" => "Description",
							"type" => "string",
							"slug" => "description"
						],
						[
							"label" => "Icon",
							"type" => "string",
							"slug" => "icon"
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "Events",
					"component_id" => $featuresId
				],
				[
					"column_name" => "description",
					"column_value" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. In officiis exercitationem ab repudiandae suscipit quidem illo non maiores excepturi. Expedita.",
					"component_id" => $featuresId
				],
				[
					"column_name" => "icon",
					"column_value" => "solid-event",
					"component_id" => $featuresId
				],
				[
					"column_name" => "heading",
					"column_value" => "Requests",
					"component_id" => $featuresId
				],
				[
					"column_name" => "description",
					"column_value" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. In officiis exercitationem ab repudiandae suscipit quidem illo non maiores excepturi. Expedita.",
					"component_id" => $featuresId
				],
				[
					"column_name" => "icon",
					"column_value" => "solid-music",
					"component_id" => $featuresId
				],
				[
					"column_name" => "heading",
					"column_value" => "DJ Portal",
					"component_id" => $featuresId
				],
				[
					"column_name" => "description",
					"column_value" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. In officiis exercitationem ab repudiandae suscipit quidem illo non maiores excepturi. Expedita.",
					"component_id" => $featuresId
				],
				[
					"column_name" => "icon",
					"column_value" => "solid-dj",
					"component_id" => $featuresId
				],
				[
					"column_name" => "heading",
					"column_value" => "Admin Portal",
					"component_id" => $featuresId
				],
				[
					"column_name" => "description",
					"column_value" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. In officiis exercitationem ab repudiandae suscipit quidem illo non maiores excepturi. Expedita.",
					"component_id" => $featuresId
				],
				[
					"column_name" => "icon",
					"column_value" => "solid-admin",
					"component_id" => $featuresId
				]
			]);

			// 5

			$faqSection = DB::table("components")->insertGetId([
				"title" => "FAQ Section",
				"slug" => "faq-section",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "section",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading"
						],
						[
							"label" => "Sub-Heading",
							"type" => "string",
							"slug" => "sub-heading"
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "Frequently Asked Questions",
					"component_id" => $faqSection
				],
				[
					"column_name" => "sub-heading",
					"column_value" => "YOUR",
					"component_id" => $faqSection
				],
			]);

			// 6

			$contatcSection = DB::table("components")->insertGetId([
				"title" => "Contact Section",
				"slug" => "contact-section",
				"persistence" => "permanent",
				"visibility" => "visible",
				"group_schema" => json_encode([
					"name" => "section",
					"maxEntities" => 1,
					"fields" => [
						[
							"label" => "Heading",
							"type" => "string",
							"slug" => "heading"
						],
						[
							"label" => "Sub-Heading",
							"type" => "string",
							"slug" => "sub-heading"
						],
						[
							"label" => "Button Text",
							"type" => "string",
							"slug" => "button-text"
						]
					]
				]),
				"create_datetime" => DateTime::getDateTime()
			]);

			DB::table("component_meta")->insert([
				[
					"column_name" => "heading",
					"column_value" => "Contact Us",
					"component_id" => $contatcSection
				],
				[
					"column_name" => "sub-heading",
					"column_value" => "GET IN TOUCH",
					"component_id" => $contatcSection
				],
				[
					"column_name" => "button-text",
					"column_value" => "Send Message",
					"component_id" => $contatcSection
				],
			]);


		}

	}
};
