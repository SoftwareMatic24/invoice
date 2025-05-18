@inject('pluginController','App\Http\Controllers\PluginController')

@if(request()["loggedInUser"]["role_title"] === "admin")
{{ $pluginController::loadWidget("quick-invoice", 'admin-dashboard', ["plugin"=>$plugin]); }}
@endif

@if(request()["loggedInUser"]["role_title"] === "user")
{{ $pluginController::loadWidget("quick-invoice", 'user-dashboard', ["plugin"=>$plugin]); }}
@endif