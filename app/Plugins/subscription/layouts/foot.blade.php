@inject('pluginController','App\Http\Controllers\PluginController')
{!! $pluginController->loadFile($parentPluginSlug, 'css/style.css') !!}
