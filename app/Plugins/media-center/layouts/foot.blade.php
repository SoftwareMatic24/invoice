@inject('projectController','App\Http\Controllers\ProjectController')
@inject('pluginController','App\Http\Controllers\PluginController')

<div class="media-center">

	<div class="media-center-body">

		<div class="media-center-sidebar">

			<div class="media-center-sidebar-header">

				<p class="margin-bottom-2 heading">{{ __("folders") }}</p>

				<button class="button button-block button-primary | new-folder-button">{{ ucwords(__("new folder")) }}</button>

				<div class="new-folder-container">
					<p class="margin-bottom-2 heading">{{ ucwords(__("new folder")) }}</p>

					<form class="new-folder-form" action="#">
						<div class="form-group">
							<input name="folder-name" type="text" class="input-style-1" placeholder="{{ __('folder name') }}">
						</div>
						<div class="form-group">
							<button data-is="save-button" class="button button-block button-primary">{{ __("save folder") }}</button>
						</div>
						<div class="section-divider" style="--width:1rem">{{ __("or") }}</div>
						<div>
							<button data-is="cancel-button" type="button" class="button button-block">{{ __("cancel") }}</button>
						</div>
					</form>
				</div>

				<ul class="media-center-sidebar-list | margin-top-3"></ul>

			</div>

		</div>

		<div class="media-center-main">

			<div class="media-center-main-header">

				<span class="media-center-close" onclick="mediaCenter.hide()">
					{!! $pluginController->loadIcon($plugin['slug'], 'cross', ['classes'=>['media-center-close']]) !!}
				</span>

				<ul class="media-center-tabs">
					<li class="active" data-name="upload">{{ ucwords(__("upload files")) }}</li>
					<li data-name="media">{{ ucwords(__("media library")) }}</li>
				</ul>

				<ul class="media-center-header-toolbar-list">
					<li onclick="mediaCenter.onUse()" data-is="use-as">
						{!! $pluginController->loadIcon($plugin['slug'], 'solid-use', ['classes'=>['icon'], 'style'=>'width:3rem; height:3rem;']) !!}
						<span class="title"></span>
					</li>
					<li onclick="mediaCenter.deleteSelectedMedia()">
						{!! $pluginController->loadIcon($plugin['slug'], 'solid-trash', ['classes'=>['icon']]) !!}
						{{ __("delete") }}
					</li>
					<li onclick="mediaCenter.moveMediaToFolder()">
						{!! $pluginController->loadIcon($plugin['slug'], 'solid-cut', ['classes'=>['icon'], 'style'=>'width:3rem; height:3rem;']) !!}
						{{ __("move") }}
					</li>
				</ul>

			</div>

			<div class="media-center-main-body">

				<div class="media-center-tabs-content">

					<div class="active" data-name="upload-content">

						<div class="media-center-upload-box" draggable="true">
							<p class="heading heading-1">{{ ucwords(__("drop your files here to upload")) }}</p>
							<p class="heading heading-2">{{ ucwords(__("drop here")) }}</p>
							<p class="sub | margin-top-1">or</p>
							<button data-is="upload-button" class="button button-default | margin-top-2">{{ ucwords(__("select files")) }}</button>
							<input class="hide" type="file" multiple>
						</div>

						<div class="media-center-progress-box">
							<div class="media-center-progress-box-header">
								<p class="media-center-progress-box-count">__("files"): 0</p>
								<button class="button button-ghost">__("cancel")</button>
							</div>

							<div class="media-center-progress-box-body">
								<ul class="media-center-progress-list"></ul>
							</div>
						</div>

					</div>

					<div data-name="media-content">
						<div class="media-center-media-container | thin-scroll-bar"></div>
					</div>

				</div>

				<div class="media-center-aside"></div>

			</div>

		</div>

	</div>

</div>

<div class="media-center-overlay"></div>

{!! $pluginController->loadFile($plugin['slug'], 'css/style.css') !!}
{!! $pluginController->loadFile($plugin['slug'], 'js/mediacenter.js') !!}

@php
echo $projectController->loadFile('resources/js/private/lazy-load.js')
@endphp


<script>
	let solidFolderIcon = `{!! $pluginController->loadIcon($plugin['slug'], 'solid-folder', ['classes'=>['icon']]) !!}`;
	let solidCloudIcon = `{!! $pluginController->loadIcon($plugin['slug'], 'solid-cloud', ['classes'=>['icon']]) !!}`;
	let solidGenericFile = `{!! $pluginController->loadIcon($plugin['slug'], 'solid-generic-file', ['classes'=>['icon']]) !!}`;

	let plain120x120 = `{!! asset('assets/plain-120x120.jpg') !!}`;
	let defaultImage300x158 = `{!! asset('assets/default-image-300x158.jpg') !!}`;
	let pluginSlug = '{{ $plugin["slug"] }}';

	let mediaCenterTexts = {
		selectFolderToUpload: '{{ __("select-folder-to-upload-notification") }}',
		uploadFinished: '{{ __("upload-finish-notification") }}',
		deleteFolder: '{{ ucwords(__("delete folder")) }}',
		saving: notificationTexts.saving,
		save: '{{ __("save") }}',
		update: '{{ __("update") }}',
		move: '{{ __("move") }}',
		or: '{{  __("or") }}',
		chooseFolder: '{{ __("choose folder") }}',
		moveFolderItemsInfo:'{{ __("move-folder-items-info") }}',
		folderName: '{{ __("folder name") }}',
		folder: '{{ __("folder") }}',
		items:'{{ __("items") }}',
		deleteFolder: '{{ __("delete folder") }}',
		file: '{{ __("file") }}',
		size: '{{ __("size") }}',
		url: '{{ __("url") }}',
		open: '{{ __("open") }}',
		playVideo: '{{ __("play video") }}'

	};

	mediaCenter.init({
		url: BASE_URL + '/api/' + pluginSlug,
		images: {
			plain120x120: plain120x120,
			defaultImage300x158: defaultImage300x158
		},
		icons: {
			solidCloud:solidCloudIcon,
			solidFolder: solidFolderIcon,
			solidGenericFile: solidGenericFile
		},
		texts: mediaCenterTexts
	});
</script>