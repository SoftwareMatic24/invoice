@extends("layouts.master")

@section("style")
<link rel="stylesheet" href="{{ asset('css/document.css') }}">
@yield("page-style")
@stop

@section("content")
@yield("main-content")
@stop


@section("script")
<script src="{{ asset('js/jsPDF.js') }}"></script>
<script src="{{ asset('js/html2pdf.min.js') }}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script src="{{ asset('js/moment-timezone.js') }}"></script>
<script>
	function addNewDocument(documentClasses, pageId, pageHTML) {
		let documentContainerEl = document.querySelector('.document-container');
		let html = `
			<div class="${documentClasses.join(' ')}">
				<div id="${pageId}" class="page">${pageHTML}</div>
			</div>
		`;
		documentContainerEl.insertAdjacentHTML('beforeend', html);
	}

	function documentIsFull(pageId, paddingYOffset = 0) {
		let pageEl = document.querySelector('#' + pageId);
		let documentEl = pageEl.closest('.document');

		let pageCords = pageEl.getBoundingClientRect();
		let documentCords = documentEl.getBoundingClientRect();

		let paddingY = parseFloat(pageCords.y - documentCords.y) + paddingYOffset;

		if (pageCords.height + (2 * paddingY) >= documentCords.height) return true;
		return false;
	}

	async function downloadPDF(fileName) {
		let n = Notification.show({
			text: "Preparing file to download",
			time: 0
		});

		let pdf = jspdf.jsPDF();
		let els = document.querySelectorAll('.document');

		let pdfPromises = [];

		let doc = null;
		for (let i = 0; i <= els.length - 1; i++) {
			let el = els[i];
			if (i === 0) doc = html2pdf().set({
				html2canvas: {
					scale: 2,
					scrollY: 0
				},
			}).from(el).toPdf();
			else {
				doc = doc.get('pdf').then((pdf) => {}).from(el).toContainer().toCanvas().toPdf();
			}
		}
		let response = await doc.save(fileName);
		Notification.hide(n.data.id);
	}
</script>
@yield('page-script')
@stop