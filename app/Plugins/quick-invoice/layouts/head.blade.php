<script>

	function formatDocumentItem(itemP){
		let item = {};
		item['item-name'] = itemP.title;
		item['item-quantity'] = itemP.quantity;
		item['item-unit-price'] = itemP.unit_price;
		item['item-unit'] = itemP.unit;
		item['item-code'] = itemP.code;
		item['item-vat'] = itemP.vat;
		return item;
	}

	function calculateItemTotal(item, discount = 0, discountType = 'percentage') {
		if(item.title !== undefined) item = formatDocumentItem(item);

		let qty = item['item-quantity'];
		let unitPrice = item['item-unit-price'];
		let vatPercentage = item['item-vat'];

		qty = parseFloat(qty);
		unitPrice = parseFloat(unitPrice);
		vatPercentage = parseFloat(vatPercentage);

		if (isNaN(qty)) qty = 0;
		if (isNaN(unitPrice)) unitPrice = 0;
		if (isNaN(vatPercentage)) vatPercentage = 0;

		let discountPrice = 0;
		let itemPrice = unitPrice * qty;

		if(discountType === 'percentage') discountPrice = (discount / 100) * itemPrice;
		else if(discountType === 'amount') discountPrice = discount;

		let itemPriceWithDiscount = itemPrice - discountPrice;
		let vatPrice = calculatePercentage(vatPercentage, itemPriceWithDiscount);

		return {
			subtotal: itemPrice,
			total: (itemPrice - discountPrice) + vatPrice,
			discountTotal: discountPrice,
			vatTotal: vatPrice
		};
	}

	function calculateItemsTotal(items, discount = 0, discountType = 'percentage') {
		if(discountType === 'amount'){
			let subtotal = items.reduce((subtotal, item) =>{
				if(item.title !== undefined) item = formatDocumentItem(item);
				subtotal += ( parseFloat(item['item-unit-price']) * parseFloat(item['item-quantity']) );
				return subtotal;
			} ,0)

		
			return items.reduce((acc, item)=>{
				if(item.title !== undefined) item = formatDocumentItem(item);

				let qty = item['item-quantity'];
				let unitPrice = item['item-unit-price'];
				let vatPercentage = item['item-vat'];

				qty = parseFloat(qty);
				unitPrice = parseFloat(unitPrice);
				vatPercentage = parseFloat(vatPercentage);

				if (isNaN(qty)) qty = 0;
				if (isNaN(unitPrice)) unitPrice = 0;
				if (isNaN(vatPercentage)) vatPercentage = 0;

				let itemPrice = unitPrice * qty;
				let decimalPercentage = (itemPrice / subtotal);
				if(isNaN(decimalPercentage)) decimalPercentage = 0;
				let itemDiscount = decimalPercentage * discount;
				let itemDiscountPrice = itemPrice - itemDiscount;
				let vatPrice = (itemDiscountPrice * vatPercentage) / 100;

				acc.vatTotal += vatPrice;
				acc.discountTotal += itemDiscount;
				acc.subtotal += itemPrice;
				acc.total += (itemDiscountPrice + vatPrice)

				return acc;
			}, {total:0, subtotal: 0, discountTotal: 0, vatTotal: 0});
		}
		return items.reduce((row, item) => {
			let obj = calculateItemTotal(item, discount, discountType);
			row.total += obj.total;
			row.subtotal += obj.subtotal;
			row.discountTotal += obj.discountTotal;
			row.vatTotal += obj.vatTotal;
			return row;
		}, {subtotal: 0, total: 0, discountTotal: 0, vatTotal: 0});
	}

</script>