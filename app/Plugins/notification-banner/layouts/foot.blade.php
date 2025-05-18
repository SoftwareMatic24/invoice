@inject('pluginController','App\Http\Controllers\PluginController')
{!! $pluginController->loadFile($plugin['slug'], 'css/style.css') !!}