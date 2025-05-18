@inject('pluginController','App\Http\Controllers\PluginController')
{!! $pluginController->loadFile($plugin['slug'], 'css/style.css') !!}
<div>

	<div >

	</div>

</div>

@section('page-script')
	<script></script>
	@parent
@stop