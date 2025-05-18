@inject('emailTemplateModel','App\Plugins\EmailTemplate\Model\EmailTemplate')
@inject('shortCodeController','App\Http\Controllers\ShortCodeController')

@php
	$mailTemplate = $emailTemplateModel->getEmailTemplateBySlug("contact-section");
	$mailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate);
	$data = $mailTemplate["data"];
@endphp

<div class="section | margin-top-20" style="box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;">
	<div class="container bg-white" style="box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;">
		<div class="inner-section" style="box-sizing: border-box;padding: 40px 40px;margin: 0;">
			<h2 class="heading-3" style="box-sizing: border-box;padding: 0;margin: 0;font-size: 24px;">
				{!! $data["Heading"] !!}
			</h2>

			@foreach($data["Text Group 1"] as $text)
			<p class="text-1 | margin-top-20" style="box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;">
				{!! $text !!}
			</p>
			@endforeach

			<a href="{{ $data['Contact Button']['link'] }}" class="button button-primary | margin-top-20" style="border:none;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;">
				{!! $data["Contact Button"]["text"] !!}
			</a>

			@foreach($data["Text Group 2"] as $text)
			<p class="text-1 | margin-top-20" style="box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;">
				{!! $text !!}
			</p>
			@endforeach
		</div>
	</div>
</div>