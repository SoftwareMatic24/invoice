var dataTableState = {
	events: {},
	instances: {}
};

function dataTable(id) {

	let PUBLIC = {
		tableId: null,
		tableEl: null,
		overrideActionEvents: true
	};

	let PRIVATE = {
		originalData: [],
		data: [],
		meta: null,
		pageSize: null,
		currentPageIndex: 0,
		rowCountStats: false,
		appliedFilters: [],
		filterElements: []
	};

	// set

	let setTableId = function (id) {
		PUBLIC.tableId = id;
	}

	let setTableEl = function (tableEl) {
		PUBLIC.tableEl = tableEl;
	}

	let setOriginalData = function (data) {
		PRIVATE.originalData = data;
	}

	let setData = function (data, setOriginal = true, pageSize = getPageSize()) {
		if (setOriginal === true) setOriginalData(data);

		let formattedData = formatDataForPagination(data, pageSize);
		return PRIVATE.data = formattedData;
	}

	let setMeta = function (meta) {
		PRIVATE.meta = meta;
	}

	let setPageSize = function (pageSize) {
		PRIVATE.pageSize = pageSize;
	}

	let setRowCountStats = function (status) {
		PRIVATE.rowCountStats = status;
	}

	let setCurrentPageIndex = function (pageIndex) {
		PRIVATE.currentPageIndex = pageIndex;
	}

	// get

	let getTableEl = function () {
		return PUBLIC.tableEl;
	}

	let getTableId = function () {
		return PUBLIC.tableId;
	}

	let getOriginalData = function () {
		return PRIVATE.originalData;
	}

	let getData = function () {
		return PRIVATE.data;
	}

	let getCurrentPageData = function () {
		let data = getData()[getCurrentPageIndex()];
		if (data === undefined) data = [];
		return data;
	}

	let getMeta = function () {
		return PRIVATE.meta;
	}

	let getRowCountStats = function () {
		return PRIVATE.rowCountStats;
	}

	let getPageSize = function () {
		return PRIVATE.pageSize;
	}

	let getCurrentPageIndex = function () {
		return PRIVATE.currentPageIndex;
	}

	let getOverrideActionEvents = function () {
		return PUBLIC.overrideActionEvents;
	}

	let getFilterElements = function () {
		return PRIVATE.filterElements;
	}

	// Push

	let pushFilterElement = function (el) {
		PRIVATE.filterElements.push(el);
		return PRIVATE.filterElements;
	}

	// add / update / delete

	let addAppliedFilter = function (option = {}) {

		/**
		 * 1. type: keyword|dateRange
		 * 2. matchType: exact|partial|null
		 */

		let {
			type = 'keyword', matchType = 'partial'
		} = option;

		PRIVATE.appliedFilters.push({
			type: type,
			matchType: matchType
		});

	}

	let clearAppliedFilters = function () {
		PRIVATE.appliedFilters = [];
	}

	// state

	let registerInstance = function (tableId, obj) {
		dataTableState.instances[tableId] = obj;
		return obj;
	}

	let registerStateEvent = function (name, func) {
		dataTableState.events[name] = func;
	};

	let stateEvent = function (name) {
		return dataTableState.events[name];
	}

	// table

	let init = function (data, options = {}) {
		let tableEl = document.querySelector(`#${id}`);
		if (tableEl === null) return console.error(`Table with ID "${id}" not found.`);

		let {
			pageSize = null, rowCountStats = false
		} = options;

		setTableId(id);
		setTableEl(tableEl);
		setMeta(parseColumnsMeta(tableEl));
		setPageSize(pageSize);
		setRowCountStats(rowCountStats);
		setData(data);

		addTableFooter(tableEl);

		initSearch(tableEl);
		generateRowsViewAndPopulate(id, getData(), getCurrentPageIndex());

		if (pageSize !== null && pageSize > 1 && data.length > 1) generateAndPopulatePaginationView();
	}

	let addTableFooter = function (tableEl) {
		let dataTableContainerEl = tableEl.closest('.data-table-container');
		if (dataTableContainerEl === null) return false;

		let footerEl = dataTableContainerEl.querySelector('.data-table-footer');
		let footerHTML = `<div class="data-table-footer"><div class="left"></div><div class="right"></div></div>`;

		if (footerEl === null) dataTableContainerEl.insertAdjacentHTML('beforeend', footerHTML);
	}

	// meta & columns

	let parseColumnsMeta = function (tableEl) {
		let thEls = tableEl.querySelectorAll('thead tr th');
		return Array.from(thEls).reduce((acc, thEl, columnIndex) => {
			let dataAttributes = thEl.dataset;
			dataAttributes.columnIndex = columnIndex;
			dataAttributes.columnText = thEl.innerHTML;
			acc.push({
				...dataAttributes
			});
			return acc;
		}, []);
	}

	let findMeta = function (name) {
		let meta = getMeta();
		let match = meta.find(row => row[name] !== undefined);

		if (match === undefined) return null;
		return match;
	}

	let tableHeaders = function () {
		let meta = getMeta();
		return meta.map(row => row.columnText);
	}

	// search

	let initSearch = function (tableEl) {
		let tableId = tableEl.getAttribute('id');

		let dataTableContainerEl = tableEl.closest('.data-table-container');
		if (dataTableContainerEl === null) return false;

		let inputSearchEl = dataTableContainerEl.querySelector('.data-table-toolbar-section.search-section .search');
		let clearButtonEl = dataTableContainerEl.querySelector('.data-table-toolbar-section.search-section .cross-icon');
		let searchEls = dataTableContainerEl.querySelectorAll('.filter-by-search');

		filterBySearchElements(searchEls);

		if (inputSearchEl === null || stateEvent(tableId + '-' + 'search') !== undefined) return false;
		if (clearButtonEl !== null) clearButtonEl.addEventListener('click', clearSearchHandler);

		inputSearchEl.addEventListener('input', searchHandler);
		registerStateEvent(id + '-' + 'search', search);
		pushFilterElement(inputSearchEl);
	}

	let searchHandler = function (e) {
		let target = e.target;
		let keyword = target.value;
		search(keyword)
	}

	let clearSearchHandler = function (e) {
		let dataTableContainerEl = getTableEl().closest('.data-table-container');
		if (dataTableContainerEl === null) return false;
		let inputSearchEl = dataTableContainerEl.querySelector('.data-table-toolbar-section.search-section .search');

		inputSearchEl.value = '';
		search('');
	}

	let filterBySearchElements = function (searchEls) {

		let searchByFilter = function () {
			let target = event.target;
			if (target !== null && target !== undefined) {
				let value = target.value;
				search(value);
			}
		}

		searchEls.forEach((fbs, fbsIndex) => {
			if (stateEvent(id + '-filter-by-search' + fbsIndex) !== undefined) return;
			fbs.addEventListener('change', searchByFilter);
			registerStateEvent(id + '-filter-by-search' + fbsIndex, searchByFilter);
			pushFilterElement(fbs);
		});
	}

	let filterByDateRange = function (fromDate, toDate, columnIndex, hasTime = false) {
		let data = getOriginalData();
		if (data.length === 0) return false;

		if (fromDate === '' || toDate === '' && data.length > 0) return resetRowsView();

		let filteredData = data.filter(row => {
			if (row[columnIndex] != undefined && dateCheck(fromDate, toDate, row[columnIndex].value)) return row;
		});

		setData(filteredData, false, filteredData.length);
		generateRowsViewAndPopulate(getTableId(), getData(), 0);
		hidePagination();
	}

	let search = function (keyword) {

		let filterEls = getFilterElements();
		let originalData = getOriginalData();
		let originalPaginationFormattedData = formatDataForPagination(originalData, originalData.length);
		if (originalPaginationFormattedData.length === 0) return;
		let filteredData = originalPaginationFormattedData[0];

		let allEmptyValues = getFilterElements().every(filterEl => {
			let value = filterEl.value;
			if (filterEl.classList.contains('filter-by-search') && value === 'all') value = null;
			if (value === null || value === '') return true;
			return false;
		});

		if (allEmptyValues === true) {
			if (getPageSize() !== null && getPageSize() > 1) changePage(0);
			showPagination();
			return resetRowsView();
		}


		for (let i = 0; i < filterEls.length; i++) {
			let filterEl = filterEls[i];

			let findInHeaderIndex = -1;
			let forceSearchType = null;
			let value = filterEl.value;
			let originalValue = value;

			let searchType = filterEl.dataset.searchType;
			if (searchType !== undefined) forceSearchType = searchType;
			else searchType = 'partial';

			if (filterEl.classList.contains('filter-by-search') && value === 'all') value = null;
			if (value === null) continue;

			if (value.includes(':') === true) {
				let headerNames = tableHeaders();
				let columnName = value.split(':')[0];
				value = value.split(':')[1];

				findInHeaderIndex = headerNames.indexOf(columnName);
				if (findInHeaderIndex === -1) value = originalValue;
				searchType = 'exact';
			}

			if (forceSearchType !== null) searchType = forceSearchType;

			filteredData = filteredData.filter(row => {
				let match = false;

				for (let i = 0; i < row.length; i++) {
					if (findInHeaderIndex !== -1 && findInHeaderIndex !== i) continue;

					let column = row[i];
					if (typeof column.value === 'string' && searchType === 'partial') match = column.value !== null && column.value !== false && column.value.toLowerCase().includes(value.toLowerCase()) === true ? true : false;
					else if (typeof column.value === 'string' && searchType === 'exact') match = column.value !== null && column.value !== false && (column.value.toLowerCase() == value.toLowerCase()) === true ? true : false;
					if (match === true) break;
				}

				if (match === true) return row;
			});
		}

		let paginationFormattedData = setData(filteredData, false, filteredData.length);
		generateRowsViewAndPopulate(getTableId(), paginationFormattedData, 0);
		hidePagination();
	}

	// pagination

	let hidePagination = function () {
		let dataTableContainerEl = getTableEl().closest('.data-table-container');
		let paginationEl = dataTableContainerEl.querySelector('.data-table-pagination');
		if (paginationEl === null) return false;
		paginationEl.classList.add('hide');
	}

	let showPagination = function () {
		let dataTableContainerEl = getTableEl().closest('.data-table-container');
		let paginationEl = dataTableContainerEl.querySelector('.data-table-pagination');
		if (paginationEl === null) return false;
		paginationEl.classList.remove('hide');
	}

	let highlightSelectedPageNumber = function () {
		let dataTableContainerEl = getTableEl().closest('.data-table-container');
		let paginationEl = dataTableContainerEl.querySelector('.data-table-pagination');
		if (paginationEl === null) return false;

		let currentPageIndex = getCurrentPageIndex();

		let paginationLiEls = paginationEl.querySelectorAll('li:not([data-is="skip"])');
		let skippingLiEls = paginationEl.querySelectorAll('li[data-is="skip"]');

		paginationLiEls.forEach((liEl, liIndex) => {
			if (liIndex == currentPageIndex + 1) liEl.classList.add('active');
			else liEl.classList.remove('active');
			liEl.classList.remove('hide');
		});


		let visibility = 4;
		let margin = 2; //2 arrows
		let paginationLengthWihoutArrows = paginationLiEls.length - margin;
		let upperLimit = 10;


		if (currentPageIndex >= visibility + 1 && paginationLengthWihoutArrows > upperLimit) {
			if (skippingLiEls[0] !== undefined) skippingLiEls[0].classList.remove('hide');
			for (let i = margin; i <= currentPageIndex - visibility; i++) {
				paginationLiEls[i].classList.add('hide');
			}

		} else if (skippingLiEls[0] !== undefined) skippingLiEls[0].classList.add('hide');


		if (paginationLengthWihoutArrows > upperLimit) {
			if (skippingLiEls[1] !== undefined) skippingLiEls[1].classList.remove('hide');

			if (currentPageIndex < upperLimit - visibility) {
				for (let i = upperLimit + 1; i <= paginationLengthWihoutArrows - 1; i++) {
					paginationLiEls[i].classList.add('hide');
				}
			} else {
				for (let i = currentPageIndex + margin + visibility; i <= paginationLengthWihoutArrows - 1; i++) {
					paginationLiEls[i].classList.add('hide');
				}
			}
		}

		if (paginationLengthWihoutArrows <= upperLimit || currentPageIndex > paginationLengthWihoutArrows - 2 - visibility) {
			if (skippingLiEls[1] !== undefined) skippingLiEls[1].classList.add('hide');
		}
	}

	let formatDataForPagination = function (data, size) {
		return chunkArray(data, size === null ? data.length : size);
	}

	let changePage = function (pageIndex) {
		let data = getData();

		if (pageIndex < 0) pageIndex = 0;
		else if (pageIndex > data.length - 1) pageIndex = data.length - 1;

		setCurrentPageIndex(pageIndex);
		generateRowsViewAndPopulate(getTableId(), data, getCurrentPageIndex());
		highlightSelectedPageNumber();
	}

	let paginationView = function () {

		let tableId = getTableId();
		let dataLength = getData().length;
		let paginationButtonsHTML = [];


		for (let i = 1; i <= dataLength; i++) {
			if (i === dataLength) paginationButtonsHTML.push(`<li class="hide" data-is="skip">...</li>`);
			paginationButtonsHTML.push(`<li onclick="dataTableChangePage('${getTableId()}', ${i - 1})">${i}</li>`);
			if (i === 1) paginationButtonsHTML.push(`<li class="hide" data-is="skip">...</li>`);
		}

		if (paginationButtonsHTML.length === 0) return '';

		return `
				<div class="data-table-pagination-container">
					<ul class="data-table-pagination">
						<li onclick="dataTableChangePage('${getTableId()}', 'prev')">
							<svg class="icon"><use xlink:href="${BASE_URL}/assets/icons.svg#solid-chevron-right" /></svg>
						</li>
						${paginationButtonsHTML.join('')}
						<li onclick="dataTableChangePage('${getTableId()}', 'next')">
							<svg class="icon"><use xlink:href="${BASE_URL}/assets/icons.svg#solid-chevron-right" /></svg>
						</li>
					</ul>
				</div>
			`;
	}

	let populatePaginationView = function (paginationHTML) {
		let dataTableContainerEl = getTableEl().closest('.data-table-container');
		if (dataTableContainerEl === null) return;
		let footerEl = dataTableContainerEl.querySelector('.data-table-footer');
		let footerLeftEl = footerEl.querySelector('.left');
		let paginationEl = dataTableContainerEl.querySelector('.data-table-pagination');

		if (paginationEl === null) footerLeftEl.insertAdjacentHTML('beforeend', paginationHTML);
	}

	let generateAndPopulatePaginationView = function () {
		let paginationHTML = paginationView();
		populatePaginationView(paginationHTML);
		highlightSelectedPageNumber();
	}

	// stats

	let showRowCountStats = function () {
		let dataTableContainerEl = getTableEl().closest('.data-table-container');
		if (dataTableContainerEl === null) return;
		let footerEl = dataTableContainerEl.querySelector('.data-table-footer');
		let footerRightEl = footerEl.querySelector('.right');
		let rowCountStatusEl = footerEl.querySelector('.row-count-status');

		if (rowCountStatusEl !== null) rowCountStatusEl.remove();

		let currentPageIndex = getCurrentPageIndex();

		let count = getData().reduce((acc, pageData, index) => {
			acc.entries += pageData.length;
			if (currentPageIndex == index) {
				acc.start = (acc.entries - pageData.length) + 1;
				acc.end = acc.entries;
			}
			return acc;
		}, {
			entries: 0,
			start: 0,
			end: 0
		});

		let statsHTML = `<div class="row-count-status"><p>Showing ${count.start} to ${count.end} of ${count.entries} entries</p></div>`;
		if (count.entries === 0) statsHTML = `<div class="row-count-status"><p></p></div>`;

		footerRightEl.insertAdjacentHTML('beforeend', statsHTML);
	}

	// rows

	let rowsView = function (tableId, paginationFormattedData, pageIndex) {
		let pageData = paginationFormattedData[pageIndex];
		if (pageData === undefined) pageData = [];

		let uidMeta = findMeta('uid');

		let views = pageData.map((rowData, rowIndex) => {
			let uniqueRowId = tableId + '-' + rowIndex;
			if (uidMeta !== null) uniqueRowId += ('-' + rowData[uidMeta.columnIndex].value)
			else uniqueRowId += ('-' + rowIndex);
			return `<tr>${rowView(uniqueRowId, rowData)}</tr>`;
		});
		return views.join('');
	}

	let rowView = function (uniqueRowId, rowData) {
		let views = rowData.map((columnData, columnIndex) => {
			let uniqueColumnId = uniqueRowId + '-' + columnIndex;
			return columnView(uniqueColumnId, columnData);
		});
		return views.join('');
	}

	let columnView = function (uniqueColumnId, columnData) {

		let globalClass = columnData.classes === undefined ? '' : columnData.classes.join(' ');
		let itemClass = columnData.itemClasses === undefined ? '' : columnData.itemClasses.join(' ');
		let attributesStr = columnData.attributes === undefined ? '' : columnData.attributes.join(' ');

		if (columnData.value === null || columnData.value === undefined) columnData.value = '';

		if (columnData.type === 'text') return textColumnView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'checkbox') return checkboxColumnView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'excerpt') return excerptColumnView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'image') return imageColumnView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'tag') return tagColumnView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'list') return listColumnView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'html') return htmlColumnView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'button-group-icon') return buttonGroupIconView(columnData, globalClass, itemClass, uniqueColumnId);
		else if (columnData.type === 'button-group') return buttonGroupView(columnData, globalClass, itemClass, uniqueColumnId);

	};

	let textColumnView = function (columnData, globalClass, itemClass) {
		let click = columnData.click !== undefined ? columnData.click : null;
		if (click !== null) return `<td class="${globalClass}"><span class="${itemClass} link-button" onclick="${click}">${columnData.value}</span></td>`;
		else return `<td class="${globalClass}">${columnData.value}</td>`;
	}

	let checkboxColumnView = function (columnData, globalClass, itemClass) {
		let click = columnData.click !== undefined ? columnData.click : null;
		return `<td class="${globalClass}"><label class="input-style-1-checkbox"><input ${columnData.checked === true ? 'checked' : ''} class="${itemClass}" type="checkbox" value="${columnData.value}" /><span></span></label></td>`;
	}

	let excerptColumnView = function (columnData, globalClass, itemClass) {
		return `<td class="${globalClass}"><span class="${itemClass} excerpt">${columnData.value}</span></td>`;
	}

	let imageColumnView = function (columnData, globalClass, itemClass) {
		return `<td class="${globalClass}"><img class="${itemClass}" src="${columnData.value}" width="40" /></td>`;
	}

	let tagColumnView = function (columnData, globalClass, itemClass) {
		return `<td class="${globalClass}"><span class="tag ${itemClass}" >${columnData.value}</span></td>`
	}

	let listColumnView = function (columnData, globalClass, itemClass) {
		let liLayout = columnData.value.reduce((acc, value) => {
			let removeIcon = '';
			if (value.remove !== undefined) {
				removeIcon = `<svg onclick="dataTableButtonClick('ability-list-' + '${value.id}')" class="icon icon-danger cross"><use xlink:href="${BASE_URL}/assets/icons.svg#cross" /></svg>`;
				if (dataTableState.events['ability-list-' + value.id] === undefined) dataTableState.events['ability-list-' + value.id] = {
					'click': value.remove
				};
			}

			return acc += `<li>${value.text} ${removeIcon}</li>`;

		}, '');

		return `<td><ul class="list action-list" role="list">${liLayout}</ul></td>`;
	}

	let htmlColumnView = function (columnData, globalClass, itemClass) {
		return `<td class="${globalClass}">${columnData.value}</td>`;
	}

	let buttonGroupIconView = function (columnData, globalClass, itemClass, uniqueColumnId) {
		let uid = splitUniqueColumnId(uniqueColumnId).uid;

		let buttonsLayout = ``;
		columnData.value.forEach(function (button, buttonIndex) {
			let classes = button.classes !== undefined ? button.classes.join(' ') : '';
			let attributeStr = button.attributes === undefined ? '' : button.attributes.join(' ');
			if (button.link !== undefined) buttonsLayout += `<a ${attributeStr} target="${button.target !== undefined ? button.target : '_self'}" href="${button.link}" class="${classes}"><svg class='icon'><use xlink:href="${BASE_URL}/assets/icons.svg#${button.icon}" /></svg></a>`;
			if (button.event !== undefined) {
				let buttonId = uniqueColumnId + '-' + buttonIndex;
				buttonsLayout += `<button ${attributeStr} data-uid="${uid}" onclick="dataTableButtonClick('${buttonId}')" class="${classes}"><svg class='icon'><use xlink:href="${BASE_URL}/assets/icons.svg#${button.icon}" /></svg></button>`;
				if (dataTableState.events[buttonId] === undefined || getOverrideActionEvents() === true) dataTableState.events[buttonId] = button.event;
			}
		});

		return `<td><div class="button-group">${buttonsLayout}</div></td>`;
	}

	let buttonGroupView = function (columnData, globalClass, itemClass, uniqueColumnId) {
		let uid = splitUniqueColumnId(uniqueColumnId).uid;

		let buttonsLayout = ``;
		columnData.value.forEach(function (button, buttonIndex) {

			let classes = button.classes !== undefined ? button.classes.join(' ') : '';
			if (button.link !== undefined) buttonsLayout += `<a target="${button.target !== undefined ? button.target : '_self'}" href="${button.link}" class="${classes}">${button.text}</a>`;
			if (button.event !== undefined) {
				let buttonId = uniqueColumnId + '-' + buttonIndex;

				buttonsLayout += `<button data-uid="${uid}" onclick="dataTableButtonClick('${buttonId}')" class="${classes}">${button.text}</button>`;
				if (dataTableState.events[buttonId] === undefined || getOverrideActionEvents() === true) dataTableState.events[buttonId] = button.event;
			}
		});

		return `<td><div class="button-group">${buttonsLayout}</div></td>`;
	}

	let populateRowsView = function (rowsHTML) {
		let tbodyEl = PUBLIC.tableEl.querySelector('tbody');
		tbodyEl.innerHTML = rowsHTML;
		if (getRowCountStats() === true) showRowCountStats();
	}

	let resetRowsView = function () {
		let originalData = getOriginalData();
		let originalPaginationFormattedData = setData(originalData, false);
		generateRowsViewAndPopulate(getTableId(), originalPaginationFormattedData, 0);

		if (originalData.length > 0) showPagination();
	}

	let generateRowsViewAndPopulate = function (tableId, paginationFormattedData, pageIndex) {
		let rowsHTML = rowsView(tableId, paginationFormattedData, pageIndex);
		populateRowsView(rowsHTML);
	}

	// other

	let splitUniqueColumnId = function (uniqueColumnId) {
		let splits = uniqueColumnId.split('-');

		let columnIndex = splits.pop();
		let uid = splits.pop();
		let rowIndex = splits.pop();
		let tableId = splits.join('-');

		return {
			columnIndex,
			rowIndex,
			uid,
			tableId
		}
	}

	let maxHeight = function (height) {
		getTableEl().closest('.data-table-container').style.maxHeight = height;
		getTableEl().closest('.data-table-container').style.overflowY = 'scroll';
	}

	PUBLIC.init = init;
	PUBLIC.getOriginalData = getOriginalData;
	PUBLIC.getData = getData;
	PUBLIC.setData = setData;
	PUBLIC.resetRowsView = resetRowsView;
	PUBLIC.changePage = changePage;
	PUBLIC.getCurrentPageData = getCurrentPageData;
	PUBLIC.getCurrentPageIndex = getCurrentPageIndex;
	PUBLIC.filterByDateRange = filterByDateRange;
	PUBLIC.maxHeight = maxHeight;
	PUBLIC.search = search;

	return registerInstance(id, PUBLIC);
}

function dataTableRowClick(rowId) {
	if (dataTableState.events[rowId] == undefined) return;
	let ev = dataTableState.events[rowId];
	ev();
}

function dataTableButtonClick(buttonId) {
	let eventType = event.type;
	let buttonEvent = dataTableState.events[buttonId] !== undefined ? dataTableState.events[buttonId] : null;

	if (buttonEvent === null) return;

	for (key__eventName in buttonEvent) {
		buttonEvent[key__eventName]()
	}
}

function dataTableChangePage(tableId, pageIndex) {
	let instance = dataTableState.instances[tableId];
	if (instance === undefined) return;

	let index = pageIndex;

	if (pageIndex === 'prev') index = instance.getCurrentPageIndex() - 1;
	else if (pageIndex === 'next') index = instance.getCurrentPageIndex() + 1;

	instance.changePage(index);
}