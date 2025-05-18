<div class="header">
	<div class="left">
		<p>{{ strtoupper($documentTypeText) }}</p>
	</div>
	<div class="right">
		<div class="right-content">
			<p>{{ strtoupper($documentTypeText) }} #</p>
			<p>Reference</p>
			<p>DATE</p>
			@if($dueDate)
			<p>{{ $documentType === 'delivery-note' ? 'DELIVERY DATE' : 'DUE DATE' }}</p>
			@endif
		</div>
		<div class="right-content">
			<p>{{ $documentNumber ?? "" }}</p>
			<p>{{ $referenceNumber ?? "" }}</p>
			<p>{{ $issueDate ?? "" }}</p>
			@if($dueDate)
			<p>{{ $dueDate ?? "" }}</p>
			@endif
		</div>
	</div>
</div>
<div>
	<div class="text-group" style="padding-top: 0;">
		<div class="text-group-col">
			<p class="text-group-heading">{{ ucfirst(strtolower($documentTypeText)) }} from</p>
			<div class="space-y-0-2">
				<p class="text-group-text">
					{{ $business["name"] ?? "" }}
				</p>
				@if(!empty($business['street']))
				<p class="text-group-text">{{ $business['street'] }}</p>
				@endif

				@if(!empty($business['street_2']))
				<p class="text-group-text">{{ $business['street_2'] }}</p>
				@endif

				<p class="text-group-text">
					{{ $business['city'] }}{{ !empty($business['province_state']) ? ',' : '' }}

					@if(!empty($business['province_state']))
					{{ $business['province_state'] }}
					@endif

					@if(!empty($business['postcode']))
					{{ $business['postcode'] }}
					@endif
				</p>

				@if(!empty($business['country']))
				<p>{{ $business['country'] }}</p>
				@endif
			</div>
		</div>
		<div class="text-group-col">
			<p class="text-group-heading">{{ ucfirst(strtolower($documentTypeText)) }} to</p>
			<div class="space-y-0-2">
				<p class="text-group-text">{{ $client["name"] }}</p>
				@if($client["addressLine2"])
				<p class="text-group-text">{{ $client["addressLine2"] }}</p>
				@endif
				@if($client["addressLine1"])
				<p class="text-group-text">{{ $client["addressLine1"] }}</p>
				@endif
			</div>
		</div>
		<div class="text-group-total">
			@if($documentType !== 'delivery-note')
			<p class="text-group-heading">{{ strtoupper($documentTypeText) }} TOTAL</p>
			<p class="price" data-is="total"></p>
			@endif
		</div>
	</div>

	<div class="text-group" style="padding-top: 0;">

		<div class="text-group-col">
			@if($orderNumber)
			<p class="text-group-text">
				Order Number: {{ $orderNumber }}
			</p>
			@endif

			@if($paymentMethod)
			<p class="text-group-text" style="margin-top:0.7rem;">
				Payment Method: {{ $paymentMethod }}
			</p>
			@endif

			@if($deliveryType)
			<p class="text-group-text" style="max-width: 35ch;margin-top:0.7rem;">
				Delivery Method: {{ $deliveryType }}
			</p>
			@endif

			@if(isset($customFields["top"]))
			@foreach($customFields["top"] as $row)
			<p class="text-group-text" style="max-width: 35ch;margin-top:0.7rem;">
				{{ $row["label"] }}: {{ $row["value"] }}
			</p>
			@endforeach
			@endif
		</div>

	</div>

	@if($salutation)
	<div>
		<p class="text-group-text" style="max-width: 40ch;">
			{{ $salutation ?? "" }}
		</p>
	</div>
	@endif

	<div class="hr"></div>

	<div class="invoice-box">
		<table>
			<thead>
				<tr>
					<th style="width: 2rem;">No.</th>
					<th>Item Description</th>
					<th style="width: 6rem;">Qty</th>
					@if($documentType !== "delivery-note")
					<th style="width: 15rem;">Unit Price</th>
					<th style="width: 8rem;">VAT</th>
					<th style="width: 15rem;">Total</th>
					@endif
				</tr>
			</thead>
			<tbody></tbody>
		</table>

	</div>

	<div>
		<div class="summary"></div>
	</div>


	<div class="text-group" style="margin-top: 2rem;">
		<div style="width: 100%;">
			@if(isset($customFields["bottom"]))
			@foreach($customFields["bottom"] as $row)
			<p class="text-group-text" style="max-width: 35ch;margin-bottom:0.5rem;">
				{{ $row["label"] }}: {{ $row["value"] }}
			</p>
			@endforeach
			@endif
			@if($note)
			<p class="text-group-text" style="max-width: 35ch;font-style: italic;">
				{{ $note }}
			</p>
			@endif
		</div>
	</div>

	<div class="footer"></div>


</div>