<div class="header">
	<div class="left">
		
		@if($business["logoURL"])
		<img class="brand-logo" src="{{ asset('storage/'.$business['logoURL']) }}" height="40" alt=" ">
		@endif
		
		<p class="brand-name">
			{{ $business["name"] ?? "" }}
		</p>

		@if(!empty($business['street']))
		<p>{{ $business['street'] }}</p>
		@endif

		@if(!empty($business['street_2']))
		<p>{{ $business['street_2'] }}</p>
		@endif

		
		<p>
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
	<div class="right">
		<h1 class="header-title">{{ $documentTypeText }}</h1>
	</div>
</div>
<div class="body">
	<div class="text-group">
		<div class="left">
			<p class="text-group-title">{{ $documentTypeText }} to:</p>
			<p class="text-group-heading">{{ $client["name"] }}</p>
			@if($client["addressLine2"])
			<p class="text-group-text">{{ $client["addressLine2"] }}</p>
			@endif
			@if($client["addressLine1"])
			<p class="text-group-text">{{ $client["addressLine1"] }}</p>
			@endif
		</div>
		<div class="right">
			<p class="text-group-title">&nbsp;</p>
			<p class="text-group-heading">{{ $documentTypeText }} # <span class="small">{{ $documentNumber ?? "" }}</span></p>
			<p class="text-group-heading">Date <span class="small">{{ $issueDate ?? "" }}</span></p>
		</div>
	</div>
	<div class="text-group" style="margin-top: 1.5rem;">
		<div class="left">
			@if($salutation)
			<p class="text-group-text" style="max-width: 35ch;font-style: italic;margin-bottom:2rem;">
				{{ $salutation ?? "" }}
			</p>
			@endif

			@if($paymentMethod)
			<p class="text-group-text" style="max-width: 35ch;">
				Payment Method: {{ $paymentMethod }}
			</p>
			@endif
			@if($deliveryType)
			<p class="text-group-text" style="max-width: 35ch;margin-top:0.5rem;">
				Delivery Method: {{ $deliveryType }}
			</p>
			@endif
			@if(isset($customFields["top"]))
			@foreach($customFields["top"] as $row)
			<p class="text-group-text" style="max-width: 35ch;margin-top:0.5rem;">
				{{ $row["label"] }}: {{ $row["value"] }}
			</p>
			@endforeach
			@endif
		</div>
		<div class="right">
			<p class="text-group-heading">Reference No. <span class="small">{{ $referenceNumber ?? "" }}</span></p>
			<p class="text-group-heading" style="margin-top: 0.5rem;">Order No. <span class="small">{{ $orderNumber ?? "" }}</span></p>
			@if($dueDate)
			<p class="text-group-heading" style="margin-top: 0.5rem;">{{ $documentType === 'delivery-note' ? 'Delivery Date' : 'Due Date' }} <span class="small">{{ $dueDate ?? "" }}</span></p>
			@endif
		</div>
	</div>
	<table style="margin-top: 2rem;">
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
	<div class="text-group" style="margin-top: 2rem;">
		<div class="left" style="width: 60%;">
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
		<div class="right border summary" style="width: 43.8%;"></div>
	</div>
</div>
<div class="footer"></div>