@inject("pm", "App\Plugins\PaymentMethods\Model\PaymentMethod")

@php
	$paymentMethods = $pm->paymentMethods();
@endphp

<div id="payment-methods-modal" class="modal" style="width: min(70rem, 90%)">
	<div class="modal-header">
		<p class="modal-title">Choose Payment Method</p>
		<span onclick="hideModal('payment-methods-modal')">
			<svg class="modal-close">
				<use xlink:href="{{ asset('assets/icons.svg#cross') }}" />
			</svg>
		</span>
	</div>
	<div class="modal-body">
		<div>
			<div class="image-icon-box-container">
				@foreach($paymentMethods as $paymentMethod)
				@if($paymentMethod["slug"] !== "cod" && $paymentMethod["slug"] !== "bank-transfer")
				<div onclick="handlePaymentMethodClick(`{{ $paymentMethod['slug'] }}`)" class="image-icon-box">
					<img class="icon" src="{{ url('plugin/payment-method/assets/'.$paymentMethod['image']) }}" alt="Cash on Delivery">
					<p class="heading">{{ $paymentMethod["title"] }}</p>
				</div>
				@endif
				@endforeach
			</div>
		</div>
	</div>

</div>

