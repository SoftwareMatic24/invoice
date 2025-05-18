<body style="box-sizing: border-box;padding: 0;margin: 0;font-family: Arial, Helvetica, sans-serif;color: #495057;background-color: #f1f3f9;">

	<div style="min-height: 50px;box-sizing: border-box;padding: 0;margin: 0;"></div>

	@include("mails/logo-section")

	<div class="section" style="box-sizing: border-box;padding: 0;margin: 0;">

		<div class="container | bg-white" style="box-sizing: border-box;padding: 0;margin: auto;background-color: #fff;width: min(600px, 98%);display: block;">

			<div class="hero" style="box-sizing: border-box;padding: 20px;margin: 0;background-color: #333338;background-image: linear-gradient(0deg, rgba(51, 51, 56, 0.9), rgba(51, 51, 56, 0.9)), url('');background-position: center;height: 180px;">
				<h2 class="heading heading-2 | text-align-center" style="box-sizing: border-box;padding: 0;margin: 0;font-size: 28px;line-height: 1.4;text-align: center;color: #fff;width: 100%;padding-top: 10%;">{{ $details["title"] }}</h2>
			</div>

			<div class="inner-section" style="box-sizing: border-box;padding: 40px 40px;margin: 0;">

				<p class="text-1" style="box-sizing: border-box;padding: 0;margin: 0;font-size: 17px;line-height: 1.4;"><b style="box-sizing: border-box;padding: 0;margin: 0;font-weight: bold;">Dear {{ $details["name"] }},</b></p>

				@foreach($details["data"]["Text Group 1"] as $text)
				<p class="text-1 | margin-top-20" style="box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;">
					{!! $text !!}
				</p>
				@endforeach

				<a href='{{ $details["resetPasswordLink"] }}' class="button button-primary | margin-top-20" style="border:none;box-sizing: border-box;padding: 14px 28px;margin: 0;margin-top: 20px;font-family: Arial, Helvetica, sans-serif;font-size: 16px;text-align: center;text-decoration: none;display: inline-block;border-radius: 3px;min-width: 165px;color: #fff;background-color: #333338;">
					{!! $details["data"]["Reset Password Button Text"] !!}
				</a>

				<p class="text-1 | margin-top-20" style="box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;">
					{!! $details["data"]["Reset Password Link Text"] !!} <a href='{{ $details["resetPasswordLink"] }}' style="box-sizing: border-box;padding: 0;margin: 0;word-break: break-all;">{{ $details["resetPasswordLink"] }}</a>
				</p>

				@foreach($details["data"]["Text Group 2"] as $text)
				<p class="text-1 | margin-top-20" style="box-sizing: border-box;padding: 0;margin: 0;margin-top: 20px;font-size: 17px;line-height: 1.4;">
					{!! $text !!}
				</p>
				@endforeach

			</div>

		</div>

	</div>

	@include("mails/contact-section")

	<div style="min-height: 100px;box-sizing: border-box;padding: 0;margin: 0;"></div>

</body>