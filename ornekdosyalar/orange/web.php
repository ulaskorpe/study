<?php

/** * OrangeTech Soft Taxi & Mietwagen Verwaltungssystem
 *
 * @package   Premium
 * @author    OrangeTech Soft <support@orangetechsoft.at>
 * @link      http://www.orangetechsoft.at/
 * @copyright 2017 OrangeTech Soft
 */

/**
 * @package OrangeTech Soft Taxi & Mietwagen Verwaltungssystem
 * @author  OrangeTech Soft <support@orangetechsoft.at>
 */


use App\Models\Job;
use App\Role;
use Vsch\TranslationManager\Translator;
use Illuminate\Support\Facades\DB;

$roles = Role::where("system_user", "=", 0)->select("name")->get();
$_roles = "|";
foreach ($roles as $role) {
    $_roles .= $role->name . "|";
}


Route::get('/', 'Manager\HomeController@index')->middleware('auth');
Route::get('/dashboard', 'Manager\HomeController@dashboard')->middleware('auth');

Route::get('/test', function () {
    return null;
  //  return \App\Models\Order::with("invoice_items")->inRandomOrder()->first();
});


Route::get('/sofort', 'HomeController@sofort')->name("sofort");


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'translations'], function () {
    Translator::routes();
});


Route::get('/pfiles', "Common\File\FileController@getFile")->middleware('auth')->name('pfiles');

Route::get('/get_file', "Common\File\FileController@getCommonFile")->name('get_file');


Route::post('/changelanguage', 'LocalizationController@changelanguage')->name("changelanguage");

Route::group(["namespace" => "Todo", "prefix" => "todo", "middleware" => "auth"], function () {

    Route::get('/all/{status?}', 'TodoController@todo')->name('todo.list');
    Route::get('/new_task_count', 'TodoController@newTaskCount')->name('todo.new_task_count');

});
Route::group(["namespace" => "User", "middleware" => "auth"], function () {

    Route::get('/edit-profile', 'UserController@editprofile')->name('edit.profile');
    Route::post('/edit-profile', 'UserController@editprofile')->name('edit.profile');

    Route::get('/change-password', 'UserController@changepassword')->name('change.password');


    Route::post('/change-password', 'UserController@changepassword');


    Route::get('/find_country_code/{location_id?}', "UserController@findCountryCode")->name('find_country_code');



    Route::group(["prefix" => "tasks", 'namespace' => 'Task'], function () {

        //    Route::get('/', 'TaskController@ok');

        Route::get('/', 'TaskController@index')->name('user.tasks.index');


        Route::get('/show_tasks/{user_array?}/{status?}/{start_at?}/{end_at?}/{priority?}', 'TaskController@show_tasks')->name('user.tasks.show_tasks');

        Route::get('/create/{start_date?}/{user_id?}', 'TaskController@create')->name('user.tasks.create');
        Route::post('/create', 'TaskController@create');
        Route::post('/driver_creates_task', 'TaskController@driverCreate');
        Route::post('/driver_company_creates_task', 'TaskController@driverCompanyCreate');


        Route::get('/timeline/{user_array?}/{status?}/{start_at?}/{end_at?}', 'TaskController@timeline')->name('user.tasks.timeline');

        Route::get('/show_timeline/{user_array?}/{status?}/{start_at?}/{mode?}/{priority?}', 'TaskController@show_timeline')->name('user.tasks.show_timeline');

        Route::get('/user_view/{task_id?}', 'TaskController@user_view')->name('user.tasks.user_view');

        Route::get('/update/{task_id?}', 'TaskController@edit')->name('user.tasks.edit');
        Route::post('/update/{task_id?}', 'TaskController@edit');
        Route::post('/update_status', 'TaskController@update_status');

        Route::get('/drag_task/{task_id?}/{start_at?}/{end_at?}', 'TaskController@drag_task')->name('user.tasks.drag_task');


        Route::get('/user_status/{task_id?}/{status?}', 'TaskController@user_status')->name('user.tasks.user_status');

        Route::get('/delete/{task_id?}', 'TaskController@delete')->name('user.tasks.delete');
        Route::post('/cancel', 'TaskController@cancel')->name('user.tasks.cancel');
        Route::get('/update_done/{task_id?}/{user_id?}', 'TaskController@update_done')->name('user.tasks.update_done');
        Route::get('/find_user/{user_id?}', 'TaskController@findUser')->name('user.tasks.findUser');
        Route::get('/timelineprocess', 'TaskController@taskProcess')->name('taskProcess');/////ajax

        Route::get('/get_users/{selected_users?}','TaskController@getUsers')->name('user.tasks.get_users');

    });

});
Route::group(["namespace" => "Auth"], function () {

    Route::get('/login', 'LoginController@login')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');
    Route::get('/register', 'RegisterController@register')->name('register');
    Route::post('/register', 'RegisterController@register');

    Route::get('/smsregister', 'RegisterController@smsregister')->name('smsregister');
    Route::get('/smsregister/{id}', 'RegisterController@smsregister')->name('smsregister');
    Route::get('/sendsms', 'RegisterController@sendsms')->name('sendsms');
    Route::get('/validatesms', 'RegisterController@validatesms')->name('validatesms');
    Route::get('/finishregister', 'RegisterController@finishregister')->name('finishregister');
    Route::get('/login-facebook', 'LoginController@redirectToProvider');
    Route::get('/login-facebook/callback', 'LoginController@handleProviderCallback');
    Route::get('/facebookregister', 'RegisterController@facebookregister');
    Route::post('/facebookregister', 'RegisterController@facebookregister');
    Route::get('/forgot-password', 'ForgotPasswordController@forgotPassword');
    Route::get('/sendresetlinkemail', 'ForgotPasswordController@sendresetlinkemail');
    Route::post('/sendresetlinkemail', 'ForgotPasswordController@sendresetlinkemail');
    Route::post('/resetpassword', 'ForgotPasswordController@resetpassword');
    Route::get('/resetpassword/{token}/{user_id}', 'ForgotPasswordController@resetpassword');


});
Route::group(['namespace' => 'Client', 'prefix' => 'client', 'middleware' => 'role:client'], function () {
    Route::get('/', 'HomeController@index')->name('client');
    //Profil
    Route::group(['prefix' => 'profile', 'namespace' => 'Profile'], function () {
        Route::get('/', "ProfileController@index")->name('client.profile.index');
        Route::post('/updateprofile', "ProfileController@updateprofile")->name('client.profile.updateprofile');
        Route::post('/updateaccount', "ProfileController@updateaccount")->name('client.profile.updateaccount');
    });
    //Order
    Route::group(['prefix' => 'order', 'namespace' => 'Order'], function () {
        //    Route::get('/', "OrderController@index")->name('client.order.index');
        //  Route::get('/orders', "OrderController@index")->name('client.order.orders');

        Route::get('/orderlist', "OrderController@orderlist")->name('client.order.index');
        Route::post('/orderlist', "OrderController@orderlist");

        Route::get('/getorder/{order_id}', 'OrderController@getorder');
        Route::get('/updateform/{job_id}', 'OrderController@updateform');
        Route::post('/updateform', 'OrderController@updateform');


    });

    Route::group(['prefix' => 'company', 'namespace' => 'Company'], function () {
        Route::get('/', "CompanyController@index")->name('client.company.index');
        Route::get('/update/{id}', "CompanyController@update")->name('client.company.update');
        Route::post('/update/{id}', "CompanyController@update");
        Route::get('/delete/{id}', 'CompanyController@delete')->name('client.company.delete');
        Route::get('/profile/{id}', 'CompanyController@profile')->name('client.company.profile');
        Route::get('/departments/{company_id}', 'CompanyController@departments')->name('client.company.departments');
        Route::post('/add-department', 'CompanyController@addDepartment');
        Route::post('/remove-department', 'CompanyController@removeDepartment');
    });

    Route::group(['prefix' => 'client', 'namespace' => 'Client'], function () {
        Route::get('/', "ClientController@index")->name('client.client.index');
        Route::get('/create', "ClientController@create")->name('client.client.create');
        Route::post('/create', "ClientController@create")->name('client.client.create');
        Route::get('/update/{id?}', "ClientController@update")->name('client.client.update');
        Route::get('/profile/{id?}', "ClientController@profile")->name('client.client.profile');
        Route::post('/update/{id?}', "ClientController@update");
        Route::get('/delete/{job_id}', 'ClientController@delete')->name('client.client.delete');
        Route::get('/find_country_code/{values?}', "ClientController@findCountryCode")->name('client.client.find_country_code');
        Route::get('/find_country/{values?}', "ClientController@findCountry")->name('client.client.find_country');
        Route::get('/add_client_company/{values?}', "ClientController@addClientCompany")->name('client.client.add_client_company');
        Route::get('/find_company/{company_id?}', "ClientController@findCompany")->name('client.client.find_company');
        Route::get('/get_departments/{company_id?}', "ClientController@getDepartments")->name('client.client.get_departments');
    });

    Route::get('/favorite-drivers', 'FavoriteDriverController@index')->name('client.favorite-drivers');
    Route::post('/favorite-drivers', 'FavoriteDriverController@index')->name('client.favorite-drivers');
});
Route::group(['namespace' => 'Manager', 'prefix' => 'manager', 'middleware' => 'role:manager|operator' . $_roles], function () use ($_roles) {

    Route::get('/', 'HomeController@index');
    Route::group(["prefix" => "activity", 'namespace' => 'Activity'], function () {
        Route::post("/getactivitylog", "ActivitylogController@getactivitylog")->middleware("permission");
        Route::get("/getactivitylog", "ActivitylogController@getactivitylog")->middleware("permission")->name("getactivitylog");

        Route::post("/getdiffrence", "ActivitylogController@getdiffrence")->middleware("permission");
        Route::get("/getdiffrence", "ActivitylogController@getdiffrence")->middleware("permission")->name("getdiffrence");


    });
    Route::group(["prefix" => "operatorlog", 'namespace' => 'OperatorLog'], function () {
        Route::post("/createclientlog", "OperatorLogController@createclientlog")->middleware("permission");
        Route::get("/createclientlog", "OperatorLogController@createclientlog")->middleware("permission")->name("createclientlog");

        Route::post("/createdrivercompanylog", "OperatorLogController@createdrivercompanylog")->middleware("permission");
        Route::get("/createdrivercompanylog", "OperatorLogController@createdrivercompanylog")->middleware("permission")->name("createdrivercompanylog");

        Route::post("/createclientcompanylog", "OperatorLogController@createclientcompanylog")->middleware("permission");
        Route::get("/createclientcompanylog", "OperatorLogController@createclientcompanylog")->middleware("permission")->name("createclientcompanylog");

    });
    Route::group(["prefix" => "releasenotifications", 'namespace' => 'Notification'], function () {
        Route::get("/release/{notification_id?}", "ReleaseNotificationController@releasenotification")->middleware("permission")->name("releasevehicle");
    });
    Route::group(['prefix' => 'client', 'namespace' => 'Client'], function () {

        Route::get('/', "ClientController@index")->middleware("permission")->name('manager.client.index');

        Route::get('/create', "ClientController@create")->middleware("permission")->name('manager.client.create');
        Route::post('/create', "ClientController@create")->middleware("permission");

        Route::get('/edit/{id?}', "ClientController@edit")->middleware("permission")->name('manager.client.edit');
        Route::post('/edit/{id?}', "ClientController@edit")->middleware("permission");

        Route::get('/delete/{id}', "ClientController@delete")->middleware("permission")->name('manager.client.delete');
        Route::post('/delete', "ClientController@delete")->middleware("permission");

        Route::get('/profile/{id}', "ClientController@profile")->middleware("permission")->name('manager.client.profile');
        Route::get('/profiledetail/{id}', "ClientController@profiledetail")->middleware("permission")->name('manager.client.profiledetail');

        Route::get('/add_client_company/{values?}', "ClientController@addClientCompany")->middleware("permission")->name('manager.client.add_client_company');
        Route::get('/find_company/{company_id?}', "ClientController@findCompany")->middleware("permission")->name('manager.client.find_company');
        Route::get('/find_country_code/{values?}', "ClientController@findCountryCode")->middleware("permission")->name('manager.client.find_country_code');
        Route::get('/find_country/{values?}', "ClientController@findCountry")->middleware("permission")->name('manager.client.find_country');
        Route::get('/get_departments/{company_id?}', "ClientController@getDepartments")->middleware("permission")->name('manager.client.get_departments');

    });
    Route::group(['prefix' => 'vehicleclass', 'namespace' => 'Vehicle'], function () {

        Route::get('/', "VehicleClassController@index")->middleware("permission")->name('manager.vehicleclass.index');

        Route::get('/edit/{class_id}', 'VehicleClassController@update')->middleware("permission")->name('manager.vehicleclass.edit');
        Route::post('/edit/{class_id}', 'VehicleClassController@update')->middleware("permission");

        Route::get('/create', 'VehicleClassController@create')->middleware("permission")->name('manager.vehicleclass.create');
        Route::post('/create', 'VehicleClassController@create')->middleware("permission");

        Route::post('/delete', 'VehicleClassController@delete')->middleware("permission")->name('manager.vehicleclass.delete');
        Route::get('/profile/{class_id}', 'VehicleClassController@profile')->middleware("permission")->name('manager.vehicleclass.profile');

    });
    Route::group(['prefix' => 'vehiclebrand', 'namespace' => 'Vehicle'], function () {

        Route::get('/', "VehicleBrandController@index")->middleware("permission")->name('manager.vehiclebrand.index');

        Route::get('/edit/{brand_id}', 'VehicleBrandController@update')->middleware("permission")->name('manager.vehiclebrand.edit');
        Route::post('/edit/{brand_id}', 'VehicleBrandController@update')->middleware("permission");

        Route::get('/create', 'VehicleBrandController@create')->middleware("permission")->name('manager.vehiclebrand.create');
        Route::post('/create', 'VehicleBrandController@create')->middleware("permission");

        Route::post('/delete', 'VehicleBrandController@delete')->middleware("permission")->name('manager.vehiclebrand.delete');
        Route::get('/profile/{brand_id}', 'VehicleBrandController@profile')->middleware("permission")->name('manager.vehiclebrand.profile');

    });
    Route::group(['prefix' => 'operator', 'namespace' => 'Operator'], function () {

        Route::get('/', "OperatorController@index")->middleware("permission")->name('manager.operator.index');

        Route::get('/edit/{operator_id}', 'OperatorController@update')->middleware("permission")->name('manager.operator.edit');
        Route::post('/edit/{operator_id}', 'OperatorController@update')->middleware("permission");

        Route::get('/create', 'OperatorController@create')->middleware("permission")->name('manager.operator.create');
        Route::post('/create', 'OperatorController@create')->middleware("permission");

        Route::post('/delete', 'OperatorController@delete')->middleware("permission")->name('manager.operator.delete');
        Route::get('/profile/{operator_id}', 'OperatorController@profile')->middleware("permission")->name('manager.operator.profile');

    });
    Route::group(['prefix' => 'vehiclemodel', 'namespace' => 'Vehicle'], function () {

        Route::get('/', "VehicleModelController@index")->middleware("permission")->name('manager.vehiclemodel.index');

        Route::get('/edit/{model_id}', 'VehicleModelController@update')->middleware("permission")->name('manager.vehiclemodel.edit');
        Route::post('/edit/{model_id}', 'VehicleModelController@update')->middleware("permission");


        Route::get('/profile/{model_id}', 'VehicleModelController@profile')->middleware("permission")->name('manager.vehiclemodel.profile');

        Route::get('/create', 'VehicleModelController@create')->middleware("permission")->name('manager.vehiclemodel.create');
        Route::post('/create', 'VehicleModelController@create')->middleware("permission");

        Route::post('/delete', 'VehicleModelController@delete')->middleware("permission")->name('manager.vehiclemodel.delete');
    });
    Route::group(['prefix' => 'vehicleproperty', 'namespace' => 'Vehicle'], function () {

        Route::get('/', "VehiclePropertyController@index")->middleware("permission")->name('manager.vehicleproperty.index');

        Route::get('/edit/{model_id}', 'VehiclePropertyController@update')->middleware("permission")->name('manager.vehicleproperty.edit');
        Route::post('/edit/{model_id}', 'VehiclePropertyController@update')->middleware("permission");

        Route::get('/create', 'VehiclePropertyController@create')->middleware("permission")->name('manager.vehicleproperty.create');
        Route::post('/create', 'VehiclePropertyController@create')->middleware("permission");

        Route::get('/delete/{class_id}', 'VehiclePropertyController@delete')->middleware("permission")->name('manager.vehicleproperty.delete');
    });
    Route::group(['prefix' => 'tank_card', 'namespace' => 'Vehicle'], function () {
        Route::get('/', "TankCardController@index")->middleware("permission")->name('manager.tank_card.index');
        Route::get('/edit/{card_id?}', 'TankCardController@update')->middleware("permission")->name('manager.tank_card.edit');
        Route::post('/edit/{card_id}', 'TankCardController@update')->middleware("permission");
        Route::get('/create', 'TankCardController@create')->middleware("permission")->name('manager.tank_card.create');
        Route::post('/create', 'TankCardController@create')->middleware("permission");
        Route::post('/delete', 'TankCardController@delete')->middleware("permission")->name('manager.tank_card.delete');
        Route::get('/profile/{card_id?}', 'TankCardController@profile')->middleware("permission")->name('manager.tank_card.profile');
    });
    Route::group(['prefix' => 'tank_card_company', 'namespace' => 'Vehicle'], function () {
        Route::get('/', "TankCardCompanyController@index")->middleware("permission")->name('manager.tank_card_company.index');
        Route::get('/edit/{card_id?}', 'TankCardCompanyController@update')->middleware("permission")->name('manager.tank_card_company.edit');
        Route::post('/edit/{card_id}', 'TankCardCompanyController@update')->middleware("permission");
        Route::get('/create', 'TankCardCompanyController@create')->middleware("permission")->name('manager.tank_card_company.create');
        Route::post('/create', 'TankCardCompanyController@create')->middleware("permission");

        Route::get('/profile/{card_id?}', 'TankCardCompanyController@profile')->middleware("permission")->name('manager.tank_card_company.profile');

        Route::post('/delete', 'TankCardCompanyController@delete')->middleware("permission")->name('manager.tank_card_company.delete');
    });
    Route::group(['prefix' => 'vehicle', 'namespace' => 'Vehicle'], function () {

        Route::get('/', "VehicleController@index")->middleware("permission")->name('manager.vehicle.index');

        Route::get('/edit/{vehicle_id}', 'VehicleController@update')->middleware("permission")->name('manager.vehicle.edit');
        Route::post('/edit/{vehicle_id}', 'VehicleController@update')->middleware("permission");

        Route::get('/create', 'VehicleController@create')->middleware("permission")->name('manager.vehicle.create');
        Route::post('/create', 'VehicleController@create')->middleware("permission");

        Route::post('/delete', 'VehicleController@delete')->middleware("permission")->name('manager.vehicle.delete');

        Route::get('/getmodels/{brand_id}', 'VehicleController@getmodels')->middleware("permission")->name('manager.vehicle.getmodels');
        Route::get('/profile/{vehicle_id}', 'VehicleController@profile')->middleware("permission")->name('manager.vehicle.profile');
        Route::get('/profiledetail/{vehicle_id?}', 'VehicleController@profiledetail')->middleware("permission")->name('manager.vehicle.profiledetail');

        Route::get('/show_clients/{vehicle_id?}', 'VehicleController@show_clients')->middleware("permission")->name('manager.vehicleclass.show_clients');
        Route::get('/show_expenses/{vehicle_id?}', 'VehicleController@show_expenses')->middleware("permission")->name('manager.vehicleclass.show_expenses');
        Route::get('/show_orders/{vehicle_id?}', 'VehicleController@show_orders')->middleware("permission")->name('manager.vehicleclass.show_orders');
    });
    Route::group(['prefix' => 'vehicle_extra', 'namespace' => 'Vehicle'], function () {

        Route::get('/', "VehicleExtraController@index")->middleware("permission")->name('manager.vehicle_extra.index');

        Route::get('/edit/{extra_id}', 'VehicleExtraController@update')->middleware("permission")->name('manager.vehicle_extra.edit');
        Route::post('/edit/{extra_id}', 'VehicleExtraController@update')->middleware("permission");

        Route::get('/create', 'VehicleExtraController@create')->middleware("permission")->name('manager.vehicle_extra.create');
        Route::post('/create', 'VehicleExtraController@create')->middleware("permission");

        Route::post('/delete', 'VehicleExtraController@delete')->middleware("permission")->name('manager.vehicle_extra.delete');


        Route::get('/profile/{extra_id}', 'VehicleExtraController@profile')->middleware("permission")->name('manager.vehicle_extra.profile');



    });
    Route::group(['prefix' => 'package', 'namespace' => 'Package', "middleware" => "role:manager" . $_roles], function () {

        Route::get('/', "PackageController@index")->middleware("permission")->name('manager.package.index');
        Route::get('/package_view/{is_private?}', "PackageController@indexView")->middleware("permission")->name('manager.package.indexView');

        Route::get('/create', 'PackageController@create')->middleware("permission")->name('manager.package.create');
        Route::post('/create', 'PackageController@create')->middleware("permission");


        Route::get('/default_ppp', 'PackageController@defaultPPP')->middleware("permission")->name('manager.package.default_ppp');
        Route::post('/default_ppp', 'PackageController@defaultPPP')->middleware("permission");

        Route::get('/edit/{package_id}', 'PackageController@edit')->middleware("permission")->name('manager.package.edit');
        Route::post('/edit/{package_id}', 'PackageController@edit')->middleware("permission");

        Route::get('/profile/{package_id}', 'PackageController@profile')->middleware("permission")->name('manager.package.profile');

        // Route::get('/delete/{package_id}', 'PackageController@delete')->name('manager.package.delete');
        Route::post('/delete', 'PackageController@delete')->middleware("permission")->name('manager.package.delete');
    });
    Route::group(['prefix' => 'driver', 'namespace' => 'Driver'], function () {

        Route::get('/', "DriverController@index")->middleware("permission")->name('manager.driver.index');

        Route::get('/create', 'DriverController@create')->middleware("permission")->name('manager.driver.create');
        Route::post('/create', 'DriverController@create')->middleware("permission");

        Route::get('/edit/{package_id}', 'DriverController@edit')->middleware("permission")->name('manager.driver.edit');
        Route::post('/edit/{package_id}', 'DriverController@edit')->middleware("permission");

        // Route::get('/delete/{package_id}', 'DriverController@delete')->middleware("permission")->name('manager.driver.delete');
        Route::post('/delete', 'DriverController@delete')->middleware("permission")->name('manager.driver.delete');
        Route::get('/profile/{id}', 'DriverController@profile')->middleware("permission")->name('manager.driver.profile');
        Route::get('/profiledetail/{id}', "DriverController@profiledetail")->middleware("permission")->name('manager.driver.profiledetail');
        Route::get('/getvehicles/{company_id?}', 'DriverController@getvehicles')->middleware("permission")->name('manager.driver.getvehicles');

        Route::get('/punishmentreason', 'PunishmentController@reasonlist')->middleware("permission")->name('manager.driver.reasonlist');
        Route::get('/createreason', 'PunishmentController@createreason')->middleware("permission")->name('manager.driver.createreason');
        Route::post('/createreason', 'PunishmentController@createreason')->middleware("permission");
        Route::get('/updatereason/{driver_punishment_reason_id}', 'PunishmentController@updatereason')->middleware("permission")->name('manager.driver.updatereason');
        Route::post('/updatereason', 'PunishmentController@updatereason')->middleware("permission");

        Route::get('/reason_info/{driver_punishment_reason_id}', 'PunishmentController@reason_info')->middleware("permission")->name('manager.driver.reason_info');

        Route::post('/deletereason', 'PunishmentController@deletereason')->middleware("permission");
        Route::get('/createpunishment/{job_id}/{driver_id}', 'PunishmentController@createpunishment')->middleware("permission")->name('manager.driver.createpunishment');
        Route::post('/createpunishment', 'PunishmentController@createpunishment')->middleware("permission");

        Route::get('/find_user_info_id/{driver_id?}', 'DriverController@find_user_info_id')->middleware("permission")->name('manager.driver.find_user_info_id');

    });
    Route::group(['prefix' => 'driver_company', 'namespace' => 'DriverCompany'], function () {

        Route::get('/', "DriverCompanyController@index")->middleware("permission")->name('manager.driver_company.index');

        Route::get('/create', 'DriverCompanyController@create')->middleware("permission")->name('manager.driver_company.create');
        Route::post('/create', 'DriverCompanyController@create')->middleware("permission");

        Route::get('/edit/{package_id}', 'DriverCompanyController@edit')->middleware("permission")->name('manager.driver_company.edit');
        Route::post('/edit/{package_id}', 'DriverCompanyController@edit')->middleware("permission");

        Route::post('/delete', 'DriverCompanyController@delete')->middleware("permission")->name('manager.driver_company.delete');

        Route::get('/profile/{id}', 'DriverCompanyController@profile')->middleware("permission")->name('manager.driver_company.profile');
        Route::get('/profiledetail/{driver_company_id}', 'DriverCompanyController@profiledetail')->middleware("permission")->name('manager.driver_company.profiledetail');
    });
    Route::group(['prefix' => 'recourses', 'namespace' => 'Recourse', "middleware" => "role:manager". $_roles], function () {

        Route::get('/', "RecourseController@index")->middleware("permission")->name('manager.recourses.index');
        Route::get('/recourse_count', "RecourseController@recourseCount")->middleware("permission")->name('manager.recourse_count');
        Route::get('/edit/{id?}', "RecourseController@edit")->middleware("permission")->name('manager.recourse.edit');
        Route::post('/edit/{id?}', "RecourseController@edit")->middleware("permission")->name('manager.recourse.edit');
        Route::get('/get_class/{model_id?}', "RecourseController@getClass")->middleware("permission")->name('manager.recourse.get_class');
        Route::get('/get_models/{brand_id?}', "RecourseController@getModels")->middleware("permission")->name('manager.recourse.get_models');
        Route::get('/get_file/{file?}', "RecourseController@getFile")->middleware("permission")->name('manager.recourse.get_file');
        Route::post('/accept_recourse', "RecourseController@acceptRecourse")->middleware("permission")->name('manager.recourse.accept_recourse');
        Route::post('/deny_recourse', "RecourseController@denyRecourse")->middleware("permission")->name('manager.recourse.deny_recourse');

        Route::get('/profile/{id?}', "RecourseController@profile")->middleware("permission")->name('manager.recourse.profile');
        ///       Route::get('/ekle', "RecourseController@recourseekle")->middleware("permission")->name('recourseekle');

        Route::post('/delete', 'RecourseController@delete')->middleware("permission")->name('manager.recourse.delete');
//        Route::get('/delete', function(){
//            return "ok";
//        })->name('manager.recourse.delete');

    });
    Route::group(['prefix' => 'client_company', 'namespace' => 'ClientCompany'], function () {

        Route::get('/', "ClientCompanyController@index")->middleware("permission")->name('manager.client_company.index');
        Route::get('/create', 'ClientCompanyController@create')->middleware("permission")->name('manager.client_company.create');
        Route::post('/create', 'ClientCompanyController@create')->middleware("permission");
        Route::get('/edit/{package_id?}', 'ClientCompanyController@edit')->middleware("permission")->name('manager.client_company.edit');
        Route::post('/edit/{package_id?}', 'ClientCompanyController@edit')->middleware("permission");
        // Route::get('/delete/{package_id}', 'ClientCompanyController@delete')->middleware("permission")->name('manager.client_company.delete');
        Route::post('/delete', 'ClientCompanyController@delete')->middleware("permission")->name('manager.client_company.delete');
        Route::get('/profile/{id}', 'ClientCompanyController@profile')->middleware("permission")->name('manager.client_company.profile');
        Route::get('/profiledetail/{id}', 'ClientCompanyController@profiledetail')->middleware("permission")->name('manager.client_company.profiledetail');
        Route::get('/gettaxrate', 'ClientCompanyController@gettaxrate')->middleware("permission")->name('manager.client_company.gettaxrate');

        Route::get('/departments/{company_id?}/{department_list?}', 'ClientCompanyController@departments')->middleware("permission")->name('manager.client_company.departments');
        Route::post('/add-department', 'ClientCompanyController@addDepartment')->middleware("permission");
        Route::post('/remove-department', 'ClientCompanyController@removeDepartment')->middleware("permission");

        Route::post('/add-department-temp', 'ClientCompanyController@addDepartmentTemp')->middleware("permission");
        Route::post('/remove-department-temp', 'ClientCompanyController@removeDepartmentTemp')->middleware("permission");

    });
    Route::group(['prefix' => 'order', 'namespace' => 'Order'], function () {
        //////////////////manager order ////////////////////////////////////////
        Route::get('/', "OrderController@timetable")->middleware("permission")->name('manager.order.index');

        Route::get('/orderlist', "OrderController@orderlist")->middleware("permission")->name('manager.order.orderlist');
        Route::post('/orderlist', "OrderController@orderlist")->middleware("permission");

        Route::get('/timetable', "OrderController@timetable")->middleware("permission")->name('manager.order.timetable');
        Route::post('/timetable', "OrderController@timetable")->middleware("permission");

        Route::get('/gettimetable/{date}/{is_alldrivers}/{vehicle_class_id}/{job_vehicle_class_id}', "OrderController@gettimetable")->middleware("permission");
        Route::get('/gettimetable/{date}/{is_alldrivers}', "OrderController@gettimetable")->middleware("permission");//hata yok silme...

        Route::get('/approveorder/{order_id}', "OrderController@approveorder")->middleware("permission");

        Route::get('/notapprovedorderlist', "OrderController@notapprovedorderlist")->middleware("permission")->name('manager.order.notapprovedorderlist');
        Route::post('/notapprovedorderlist', "OrderController@notapprovedorderlist")->middleware("permission");
        //endregion
        //endregion

    });
    Route::group(['prefix' => 'calculate', 'namespace' => 'Calculate'], function () {
        Route::get('/', "CalculateController@index")->middleware("permission")->name('manager.calculate.index');
        Route::get('/calculator', "CalculateController@calculator")->middleware("permission")->name('manager.calculate.calculator');
        Route::get('/getdata', "CalculateController@getdata")->middleware("permission")->name('manager.calculate.getdata');
        Route::post('/getdata', "CalculateController@getdata")->middleware("permission");
        Route::get('/savecalculate', "CalculateController@savecalculate")->middleware("permission")->name('manager.calculate.savecalculate');
        Route::post('/savecalculate', "CalculateController@savecalculate")->middleware("permission");
        Route::get('/getcalculate/{calculate_id}', "CalculateController@getcalculate")->middleware("permission")->name('manager.calculate.getcalculate');
        Route::post('/delete', "CalculateController@delete")->middleware("permission")->name('manager.calculate.delete');
        Route::get('/cuteoff/{calculate_item_id}', "CalculateController@cuteoff")->middleware("permission")->name('manager.calculate.cuteoff');
        Route::get('/getperiod', "CalculateController@getperiod")->middleware("permission")->name('manager.calculate.getperiod');
        Route::get('/detailcalculate/{calculate_id}', "CalculateController@detailcalculate")->middleware("permission");
        Route::get('/detailcalculate', "CalculateController@detailcalculate")->middleware("permission")->name('manager.calculate.detailcalculate');
        Route::get('/report', "CalculateController@report")->middleware("permission")->name('manager.calculate.report');
        Route::get('/getcompanyperiods', "CalculateController@getcompanyperiods")->middleware("permission")->name('manager.calculate.getcompanyperiods');

    });
    Route::group(['prefix' => 'setting', 'namespace' => 'Setting', "middleware" => "role:manager" . $_roles], function () {

        Route::get('/', "SettingController@index")->middleware("permission")->name('manager.setting.index');
        Route::post('/', "SettingController@index")->middleware("permission")->name('manager.setting.index');

        Route::get('/company_address_add', "SettingController@companyAddressAdd")->middleware("permission")->name('manager.setting.company_address_add');
        Route::post('/company_address_add', "SettingController@companyAddressAdd")->middleware("permission");

        Route::get('/company_address_update/{id?}', "SettingController@companyAddressUpdate")->middleware("permission")->name('manager.setting.company_address_update');
        Route::post('/company_address_update/{id?}', "SettingController@companyAddressUpdate")->middleware("permission");
        Route::post('/company_address_delete', "SettingController@deleteAddress")->middleware("permission")->name('manager.company_address_delete');
        Route::get('/company_settings', "SettingController@company_settings")->middleware("permission")->name('manager.setting.company_settings');
        Route::post('/company_settings', "SettingController@company_settings")->middleware("permission");
        Route::get('/environment_settings', "SettingController@environment_settings")->middleware("permission")->name('manager.setting.environment_settings');
        Route::post('/environment_settings', "SettingController@environment_settings")->middleware("permission");

//         Route::get('/cron_job_settings', "SettingController@cron_job_settings")->name('manager.setting.cron_job_settings');
//        Route::post('/cron_job_settings', "SettingController@cron_job_settings");


        Route::get('/template_settings', "SettingController@template_settings")->middleware("permission")->name('manager.template_setting');
        Route::post('/template_settings', "SettingController@template_settings")->middleware("permission")->name('manager.template_setting');
        Route::get('/update_setting/{id?}/{value?}/{tip?}', "SettingController@updateSetting")->middleware("permission")->name('manager.update.setting');
        Route::get('/edit/{package_id}', 'SettingController@edit')->middleware("permission")->name('manager.setting.edit');
        Route::post('/edit/{package_id}', 'SettingController@edit')->middleware("permission");

        Route::post('/pw_check', 'SettingController@pw_check')->middleware("permission")->name('pw_check');

        Route::group(['prefix' => 'tags'], function () {
            Route::get('/', 'TagController@index')->middleware("permission")->name('manager.setting.tags');
            Route::get('/create', 'TagController@create')->middleware("permission")->name('manager.setting.tags.create');
            Route::post('/create', 'TagController@create')->middleware("permission");

            Route::get('/edit/{tag_id?}', 'TagController@edit')->middleware("permission")->name('manager.setting.tags.edit');
            Route::post('/edit/{tag_id?}', 'TagController@edit')->middleware("permission");

            //   Route::get('/delete/{tag_id?}', 'TagController@delete')->name('manager.setting.tags.delete');
            Route::post('/delete', 'TagController@delete')->middleware("permission")->name('manager.setting.tags.delete');
            Route::get('/show/{tag_id?}', 'TagController@show')->middleware("permission")->name('manager.setting.tags.show');
        });

        Route::group(['prefix' => 'cron_job'], function () {
            Route::get('/', 'CronJobController@index')->middleware("permission")->name('manager.setting.cron_jobs');
            Route::get('/create', 'CronJobController@create')->middleware("permission")->name('manager.setting.cron_job.create');
            Route::post('/create', 'CronJobController@create')->middleware("permission");

            Route::get('/update/{tag_id?}', 'CronJobController@update')->middleware("permission")->name('manager.setting.cron_job.update');
            Route::post('/update/{tag_id?}', 'CronJobController@update')->middleware("permission");

            Route::get('/profile/{tag_id?}', 'CronJobController@profile')->middleware("permission")->name('manager.setting.cron_job.profile');

            Route::post('/delete', 'CronJobController@delete')->middleware("permission")->name('manager.setting.cron_job.delete');

        });
        Route::group(['prefix' => 'rolepermission'], function () {
            Route::get('/', 'RolePermissionController@index')->middleware("role:manager")->name('manager.setting.rolepermission');

            Route::get('/rolecreate', 'RolePermissionController@rolecreate')->middleware("role:manager")->name('manager.setting.rolecreate');
            Route::post('/rolecreate', 'RolePermissionController@rolecreate')->middleware("role:manager");

            Route::get('/roleupdate', 'RolePermissionController@roleupdate')->middleware("role:manager")->name('manager.setting.rolecreate');
            Route::post('/roleupdate', 'RolePermissionController@roleupdate')->middleware("role:manager");

            Route::post('/roledelete', 'RolePermissionController@roledelete')->middleware("role:manager");
            Route::get('/getpermissions', 'RolePermissionController@getpermissions')->middleware("role:manager")->name('manager.setting.getpermissions');
            Route::post('/savepermissions', 'RolePermissionController@savepermissions')->middleware("role:manager");
        });


    });
    Route::group(['prefix' => 'invoice', 'namespace' => 'Invoice'], function () {
        Route::get('/', "InvoiceController@index")->middleware("permission")->name('manager.invoice.index');
        Route::get('/getbydate', "InvoiceController@getbydate")->middleware("permission")->name('manager.invoice.getbydate');
        Route::get('/filter', "InvoiceController@filter")->middleware("permission")->name('manager.invoice.filter');
        Route::get('/invoice/{invoiceid}', "InvoiceController@invoice")->middleware("permission")->name('manager.invoice.invoice');

        Route::get('/cancel/{invoiceid}', "InvoiceController@cancel")->middleware("permission")->name('manager.invoice.cancel');

        Route::get('/makepayment', "InvoiceController@makepayment")->middleware("permission")->name('manager.invoice.makepayment');
        Route::get('/create', "InvoiceController@create")->middleware("permission")->name('manager.invoice.create');
        Route::post('/create', "InvoiceController@create")->middleware("permission");
        Route::get('/getforinvoiceselect/{is_company}', "InvoiceController@getforinvoiceselect")->middleware("permission");
        Route::post('/calculatetax', "InvoiceController@calculatetax")->middleware("permission");

    });
    Route::group(['prefix' => 'alltemplates', 'namespace' => 'Template'], function () {
        Route::get('/', "AlltemplateController@index")->middleware("permission")->name('manager.alltemplates.index');
    });
    Route::group(['prefix' => 'invoicetemplate', 'namespace' => 'Template'], function () {

        Route::get('/', "InvoiceTemplateController@index")->middleware("permission")->name('manager.invoicetemplate.index');

        Route::get('/test', "InvoiceTemplateController@test")->middleware("permission");

        Route::get('/create', "InvoiceTemplateController@create")->middleware("permission")->name('manager.invoicetemplate.create');
        Route::post('/create', "InvoiceTemplateController@create")->middleware("permission");

        Route::get('/edit/{invoice_template_id}', 'InvoiceTemplateController@edit')->middleware("permission")->name('manager.invoicetemplate.edit');
        Route::post('/edit/{invoice_template_id}', 'InvoiceTemplateController@edit')->middleware("permission");

        // Route::get('/delete/{invoice_template_id}', 'InvoiceTemplateController@delete')->name('manager.invoicetemplate.delete');
        Route::post('/delete', 'InvoiceTemplateController@delete')->middleware("permission")->name('manager.invoicetemplate.delete');
    });
    Route::group(['prefix' => 'mailtemplate', 'namespace' => 'Template'], function () {

        Route::get('/', "MailTemplateController@index")->middleware("permission")->name('manager.mailtemplate.index');
        // Route::get('/delete/{mail_template_id}', 'MailTemplateController@delete');
        Route::post('/delete', 'MailTemplateController@delete')->middleware("permission");
        //AJAX
        Route::get('/gettemplate/{id}', 'MailTemplateController@gettemplate')->middleware("permission");
        Route::post('/savetemplate', 'MailTemplateController@savetemplate')->middleware("permission");
    });
    Route::group(['prefix' => 'smstemplate', 'namespace' => 'Template'], function () {

        Route::get('/', "SmsTemplateController@index")->middleware("permission")->name('manager.smstemplate.index');
        //  Route::get('/delete/{mail_template_id}', 'SmsTemplateController@delete');
        Route::post('/delete', 'SmsTemplateController@delete')->middleware("permission");
        //AJAX
        Route::get('/gettemplate/{id}', 'SmsTemplateController@gettemplate')->middleware("permission");
        Route::post('/savetemplate', 'SmsTemplateController@savetemplate')->middleware("permission");
    });
    Route::group(['prefix' => 'notificationtemplate', 'namespace' => 'Template'], function () {

        Route::get('/', "NotificationTemplateController@index")->middleware("permission")->name('manager.notificationtemplate.index');
        //  Route::get('/delete/{mail_template_id}', 'NotificationTemplateController@delete');
        Route::post('/delete', 'NotificationTemplateController@delete')->middleware("permission");
        //AJAX
        Route::get('/gettemplate/{id}', 'NotificationTemplateController@gettemplate')->middleware("permission");
        Route::post('/savetemplate', 'NotificationTemplateController@savetemplate')->middleware("permission");
    });
    Route::group(['prefix' => 'personnel', 'namespace' => 'Personnel'], function () {

        Route::get('/personnels/{department_id?}', 'PersonnelController@index')->middleware("permission")->name('manager.personnel.index');

        Route::get('/create', 'PersonnelController@personnel_create')->middleware("permission")->name('manager.personnel.personnel_create');
        Route::post('/create', 'PersonnelController@personnel_create')->middleware("permission")->name('manager.personnel.personnel_create');

        Route::get('/update/{personnel_id?}', 'PersonnelController@personnel_update')->middleware("permission")->name('manager.personnel.personnel_update');
        Route::post('/update/{personnel_id?}', 'PersonnelController@personnel_update')->middleware("permission");

        Route::get('/profile/{personnel_id?}', 'PersonnelController@profile')->middleware("permission")->name('manager.personnel.profile');

        Route::post('/personnel_delete', "PersonnelController@personnel_delete")->middleware("permission")->name('manager.personnel.personnel_delete');

        Route::group(['prefix' => 'department'], function () {

            Route::get('/list', 'PersonnelController@departments_list')->middleware("permission")->name('manager.personnel.departments');
            Route::get('/create', 'PersonnelController@department_create')->middleware("permission")->name('manager.personnel.department_create');
            Route::post('/create', 'PersonnelController@department_create')->middleware("permission")->name('manager.personnel.department_create');

            Route::get('/update/{department_id?}', 'PersonnelController@department_update')->middleware("permission")->name('manager.personnel.department_update');
            Route::post('/update/{department_id?}', 'PersonnelController@department_update')->middleware("permission")->name('manager.personnel.department_update');

            Route::post('/delete', "PersonnelController@department_delete")->middleware("permission")->name('manager.personnel.department_delete');

        });

        Route::group(['prefix' => 'files'], function () {

            Route::get('/{user_id?}', 'PersonnelController@personnel_files')->middleware("permission")->name('manager.personnel.files');

            Route::get('/create/{user_id?}', 'PersonnelController@file_create')->middleware("permission")->name('manager.personnel.file_create');
            Route::post('/create', 'PersonnelController@file_create')->middleware("permission")->name('manager.personnel.file_create');

            Route::post('/update/', 'PersonnelController@file_update')->middleware("permission")->name('manager.personnel.file_update');
            Route::get('/update/{file_id?}', 'PersonnelController@file_update')->name('manager.personnel.file_update')->middleware("permission");
            Route::post('/update/{file_id?}', 'PersonnelController@file_update')->middleware("permission");

            Route::post('/delete', "PersonnelController@file_delete")->middleware("permission")->name('manager.personnel.file_delete');

        });


        Route::group(['prefix' => 'vacation'], function () {

            Route::get('/{user_id?}', 'PersonnelController@vacations')->middleware("permission")->name('manager.personnel.vacations');

            Route::get('/create/{user_id?}', 'PersonnelController@vacation_create')->middleware("permission")->name('manager.personnel.vacation_create');
            Route::post('/create', 'PersonnelController@vacation_create')->middleware("permission")->name('manager.personnel.vacation_create');

            Route::get('/update/{vacation_id?}', 'PersonnelController@vacation_update')->middleware("permission")->name('manager.personnel.vacation_update');
            Route::post('/update/{vacation_id?}', 'PersonnelController@vacation_update')->middleware("permission");

            Route::post('/delete', "PersonnelController@vacation_delete")->middleware("permission")->name('manager.personnel.vacation_delete');

        });


        Route::group(['prefix' => 'work', 'namespace' => 'Schedule'], function () {
            Route::get('/liste/{user_id}', 'ScheduleController@departmentwork')->middleware("permission")->name('manager.personnel.work');
            Route::get('/create/{user_id?}', 'ScheduleController@workcreate')->middleware("permission")->name('manager.personnel.work.create');
            Route::post('/workcreate', 'ScheduleController@workprocess')->middleware("permission")->name('manager.personnel.workcreate');
            Route::get('/workdelete/{work_id?}', 'ScheduleController@workdelete')->middleware("permission")->name('manager.personnel.work.delete');
            Route::get('/edit/{work_id?}', 'ScheduleController@workupdate')->middleware("permission")->name('manager.personnel.work.edit');
            Route::get('/weekly/{user_id?}', 'ScheduleController@workweekly')->middleware("permission")->name('manager.personnel.workweekly');
            Route::post('/weekly/update', 'ScheduleController@workweeklyupdate')->middleware("permission")->name('manager.personnel.workcreateprocess');
            Route::get('/timelineprocess', 'ScheduleController@weeklyProcess')->middleware("permission")->name('personnel.weeklyProcess');/////ajax
            Route::get('/timelineprocess', 'ScheduleController@weeklyProcess')->middleware("permission")->name('personnel.weeklyProcess');/////ajax
            Route::get('/template/{user_id?}', 'ScheduleController@workTemplate')->middleware("permission")->name('personnel.workTemplate');
            Route::get('/timelinetemplateprocess', 'ScheduleController@workTemplateProcess')->middleware("permission")->name('personnel.weeklyTemplateProcess');
            Route::get('/template/assign/{user_id?}', 'ScheduleController@workTemplateAssign')->middleware("permission")->name('personnel.workTemplate.assign');
            Route::post('/templateassign', 'ScheduleController@templateassign')->middleware("permission")->name('manager.personnel.templateassign');


            Route::get('/share/template/{user_id?}/{role_id?}/{show?}', "ScheduleController@shareTemplate")->middleware("permission")->name('manager.personnel.sharetemplate');
            Route::post('/share/process', 'ScheduleController@shareprocess')->middleware("permission")->name('manager.personnel.shareprocess');

        });


        Route::get('/find_user_id/{user_info_id}', 'PersonnelController@find_user_id')->middleware("permission")->name('find_user_id');

    });
    Route::group(['prefix' => 'expenses', 'namespace' => 'Expense'], function () {
////personel crud
///
///
///
        Route::get('/', "ExpenseController@index")->middleware("permission")->name('manager.expenses.index');
        Route::get('/edit/{expense_id?}', "ExpenseController@edit")->middleware("permission")->name('manager.expenses.edit');
        Route::post('/edit/{expense_id?}', "ExpenseController@edit")->middleware("permission");

        Route::get('/profile/{expense_id?}', "ExpenseController@profile")->middleware("permission")->name('manager.expenses.profile');

        Route::post('/delete', "ExpenseController@delete")->middleware("permission")->name('manager.expenses.delete');
        Route::get('/create/', "ExpenseController@create")->middleware("permission")->name('manager.expenses.create');
        Route::post('/create/', "ExpenseController@create")->middleware("permission");

        Route::get('/downloadfile/{expense_id?}', 'ExpenseController@downloadfile')->middleware("permission")->name('manager.expenses.downloadfile');

        Route::get('/get_tank_cards/{driver_id?}', "ExpenseController@getTankCards")->middleware("permission")->name('manager.expenses.getTankCards');
        Route::get('/get_company_drivers/{company_id?}', "ExpenseController@getCompanyDrivers")->middleware("permission")->name('manager.expenses.getCompanyDrivers');
        Route::get('/get_company_vehicles/{company_id?}', "ExpenseController@getCompanyVehicles")->middleware("permission")->name('manager.expenses.getCompanyVehicles');
        //getTankCards
        Route::group(['prefix' => 'types'], function () {
            Route::get('/', "ExpenseTypeController@index")->middleware("permission")->name('manager.expenses.types.index');
            Route::get('/edit/{expense_type_id?}', "ExpenseTypeController@edit")->middleware("permission")->name('manager.expenses.types.edit');
            Route::post('/edit/{expense_type_id?}', "ExpenseTypeController@edit")->middleware("permission");
            //Route::get('/delete/{expense_type_id?}', "ExpenseTypeController@delete")->middleware("permission")->name('manager.expenses.types.delete');
            Route::post('/delete', "ExpenseTypeController@delete")->middleware("permission")->name('manager.expenses.types.delete');
            Route::get('/create/', "ExpenseTypeController@create")->middleware("permission")->name('manager.expenses.types.create');
            Route::post('/create/', "ExpenseTypeController@create")->middleware("permission")->name('manager.expenses.types.create.post');
        });


    });
    Route::group(['prefix' => 'downloads'], function () {

        Route::get('/driverapk', 'HomeController@driverapk')->middleware("permission")->name('manager.downloads.driverapk');
        Route::get('/clientapk', 'HomeController@clientapk')->middleware("permission")->name('manager.downloads.clientapk');
    });
});

Route::group(['namespace' => 'DriverCompany', 'prefix' => 'driver_company', 'middleware' => 'role:driver_company'], function () {
    Route::get('/', 'HomeController@index')->name('driver_company');

    Route::group(['prefix' => 'vehicle', 'namespace' => 'Vehicle'], function () {

        Route::get('/', "VehicleController@index")->name('driver_company.vehicle.index');

        Route::get('/edit/{vehicle_id}', 'VehicleController@update')->name('driver_company.vehicle.edit');
        Route::post('/edit/{vehicle_id}', 'VehicleController@update');


        Route::get('/profile/{vehicle_id}', 'VehicleController@profile')->name('driver_company.vehicle.profile');

        Route::get('/create', 'VehicleController@create')->name('driver_company.vehicle.create');
        Route::post('/create', 'VehicleController@create');

        Route::post('/delete', 'VehicleController@delete')->name('driver_company.vehicle.delete');

        Route::get('/getmodels/{brand_id}', 'VehicleController@getmodels')->name('driver_company.vehicle.getmodels');


    });

    Route::group(['prefix' => 'expenses', 'namespace' => 'Expense'], function () {

        Route::get('/', "ExpenseController@index")->name('driver_company.expenses.index');
        Route::get('/edit/{expense_id?}', "ExpenseController@edit")->name('driver_company.expenses.edit');
        Route::post('/edit/{expense_id?}', "ExpenseController@edit");

        Route::get('/create/', "ExpenseController@create")->name('driver_company.expenses.create');
        Route::post('/create/', "ExpenseController@create");

        Route::post('/delete/{expense_id?}', "ExpenseController@delete")->name('driver_company.expenses.delete');

        Route::get('/profile/{expense_id?}', "ExpenseController@profile");


        Route::get('/get_tank_cards/{driver_id?}', "ExpenseController@getTankCards")->name('driver_company.expenses.getTankCards');
        Route::get('/get_company_drivers/{company_id?}', "ExpenseController@getCompanyDrivers")->name('driver_company.expenses.getCompanyDrivers');
        Route::get('/get_company_vehicles/{company_id?}', "ExpenseController@getCompanyVehicles")->name('driver_company.expenses.getCompanyVehicles');

    });
    Route::group(['prefix' => 'order', 'namespace' => 'Order'], function () {
        Route::get('/orderlist/{driver_id?}', "OrderController@orderlist")->name('driver_company.order.orderlist');
        Route::post('/orderlist', "OrderController@orderlist");
        Route::get('/getorder/{order_id?}', 'OrderController@getorder');

    });
    Route::group(['prefix' => 'driver', 'namespace' => 'Driver'], function () {



        Route::get('/', "DriverController@index")->name('driver_company.driver.index');

        Route::get('/create', 'DriverController@create') ->name('driver_company.driver.create');
        Route::post('/create', 'DriverController@create');

        Route::get('/edit/{driver_id}', 'DriverController@edit')->name('driver_company.driver.edit');
        Route::post('/edit/{driver_id}', 'DriverController@edit');


        Route::post('/delete', 'DriverController@delete')->name('driver_company.driver.delete');

        Route::get('/profile/{id}', 'DriverController@profile')->name('driver_company.driver.profile');
        Route::get('/profiledetail/{id}', "DriverController@profiledetail")->name('driver_company.driver.profiledetail');


        Route::get('/driver_details/{id?}/{show?}/{start_at?}/{end_at?}', 'DriverController@driver_details')->name('driver_company.driver.driver_details');
        Route::get('/driver_details_table/{id?}/{show?}/{start_at?}/{end_at?}', 'DriverController@driver_details_table')->name('driver_company.driver.driver_details');

        Route::get('/expense_detail/{id?}', 'DriverController@expense_detail')->name('driver_company.driver.expense_detail');
        Route::get('/order_detail/{id?}', 'DriverController@order_detail')->name('driver_company.driver.order_detail');


        ////////user_info , user_files , user_vacation vb tablolarda , foreignkey  olarak driver_id değil user_id kullanıldı
        Route::get('/find_user_info_id/{driver_id?}', 'DriverController@find_user_info_id')->name('manager.driver.find_user_info_id');


        Route::group(['prefix' => 'files'], function () {

            Route::get('/{user_id?}', 'DriverController@personnel_files')->name('manager.personnel.files');

            Route::get('/create/{user_id?}', 'DriverController@file_create')->name('manager.personnel.file_create');
            Route::post('/create', 'DriverController@file_create')->name('manager.personnel.file_create');

            Route::post('/update/', 'DriverController@file_update')->name('manager.personnel.file_update');
            Route::get('/update/{file_id?}', 'DriverController@file_update')->name('manager.personnel.file_update');
            Route::post('/update/{file_id?}', 'DriverController@file_update');
            Route::post('/delete', "DriverController@file_delete")->name('manager.personnel.file_delete');

        });


        Route::group(['prefix' => 'vacation'], function () {

            Route::get('/{user_id?}', 'DriverController@vacations') ->name('manager.personnel.vacations');

            Route::get('/create/{user_id?}', 'DriverController@vacation_create') ->name('manager.personnel.vacation_create');
            Route::post('/create', 'DriverController@vacation_create') ->name('manager.personnel.vacation_create');

            Route::get('/update/{vacation_id?}', 'DriverController@vacation_update') ->name('manager.personnel.vacation_update');
            Route::post('/update/{vacation_id?}', 'DriverController@vacation_update') ;

            Route::post('/delete', "DriverController@vacation_delete") ->name('manager.personnel.vacation_delete');

        });


    });
});
Route::group(['namespace' => 'Driver', 'prefix' => 'driver', 'middleware' => 'role:driver'], function () {
    Route::get('/', 'Profile\ProfileController@index')->name('driver.profile.index');
    Route::post('/updateprofile', 'Profile\ProfileController@updateprofile')->name('driver.profile.updateprofile');
    Route::get('/getmodels/{id?}', 'Profile\ProfileController@getmodels')->name('driver.getmodels');

    Route::group(['prefix' => 'expenses', 'namespace' => 'Expense'], function () {

        Route::get('/', "ExpenseController@index")->name('driver.expenses.index');
        Route::get('/edit/{expense_id?}', "ExpenseController@edit")->name('driver.expenses.edit');
        Route::post('/edit/{expense_id?}', "ExpenseController@edit");
        Route::get('/profile/{expense_id?}', "ExpenseController@profile");
        //Route::get('/delete/{expense_id?}', "ExpenseController@delete")->name('driver.expenses.delete');

        Route::post('/delete', "ExpenseController@delete")->name('driver.expenses.delete');
        Route::get('/create/', "ExpenseController@create")->name('driver.expenses.create');
        Route::post('/create/', "ExpenseController@create");
        Route::get('/get_tank_cards/', "ExpenseController@getTankCards")->name('driver.expenses.getTankCards');

    });


    Route::group(['prefix' => 'order', 'namespace' => 'Order'], function () {
        //////////////////manager order ////////////////////////////////////////
        //'driver.order.order_history'

        Route::get('/orderlist', "OrderController@orderlist")->name('driver.order.orderlist');
        Route::post('/orderlist', "OrderController@orderlist");


        Route::get('/orderhistory', "OrderController@orderhistory")->name('driver.order.order_history');
        Route::post('/orderhistory', "OrderController@orderhistory");


        Route::post('/accept_job', "OrderController@accept_job");
        Route::post('/deny_job', "OrderController@deny_job");


        /*  Route::get('/timetable', "OrderController@timetable")->name('driver.order.timetable');
          Route::post('/timetable', "OrderController@timetable");*/


    });

});
Route::group(['namespace' => 'Common', 'prefix' => 'common', "middleware" => "auth"], function () {
    Route::group(['prefix' => 'autocomplate', 'namespace' => 'Autocomplate'], function () {

        Route::get('/select2', "AutocomplateController@select2")->name('common.autocomplate.select2');
        Route::post('/select2', "AutocomplateController@select2");

    });
    Route::group(['prefix' => 'datatable', 'namespace' => 'Datatable'], function () {
        Route::get('/main', "DatatableController@main")->name('common.datatable.main');
        Route::post('/main', "DatatableController@main");
    });
    Route::group(['prefix' => 'location', 'namespace' => 'Location'], function () {
        Route::get('/curlgeocode', "LocationController@curlgeocode")->name('common.location.curlgeocode');
        Route::post('/curlgeocode', "LocationController@curlgeocode");

        Route::get('/addressnote', "LocationController@addressnote")->name('common.location.addressnote');
        Route::post('/addressnote', "LocationController@addressnote");

        Route::get('/placebound', "LocationController@placebound")->name('common.location.placebound');
        Route::post('/placebound', "LocationController@placebound");

        Route::get('/addtoclientfavorite', "LocationController@addtoclientfavorite")->name('common.location.addtoclientfavorite');

    });
    Route::group(['namespace' => 'Client', 'prefix' => 'client'], function () {

        ///   Route::get('/get_clients/{company_id?}/{department_id?}','Client/ClientController@getClients')->name('common.get_clients');

    });
    Route::group(['namespace' => 'ClientCompany', 'prefix' => 'client_company'], function () {

        Route::get('/get_companies/{client_id?}', 'ClientCompanyController@getCompanies');
        Route::get('/get_departments/{client_id?}/{company_id?}', 'ClientCompanyController@getDepartments');

    });

    Route::group(['namespace' => 'Order', 'prefix' => 'order'], function () {//////common order

        Route::get('/create', "OrderController@create")->middleware("permission")->name('common.order.create');
        Route::post('/create', "OrderController@create")->middleware("permission");

        Route::get('/getpricesummary', 'OrderController@getpricesummary');
        Route::post('/getpricesummary', 'OrderController@getpricesummary');

        Route::get('/delete/{job_id}', 'OrderController@delete')->middleware("permission");

        Route::get('/delete/deleteorderextra/{order_extra_id}', 'OrderController@deleteorderextra')->middleware("permission");
        Route::get('/deleteorderextra/{order_extra_id}', 'OrderController@deleteorderextra')->middleware("permission");
        Route::get('/deletepassenger/{order_passenger_id}', 'OrderController@deletepassenger')->middleware("permission");

        Route::get('/getorderhistory/{client_id}', 'OrderController@getorderhistory');
        Route::get('/get_special_packages/{client_id?}', 'OrderController@getSpecialPackages');
        Route::get('/createreorder/{orderid}', "OrderController@createreorder")->middleware("permission")->name('common.order.createreorder');
        Route::post('/createreorder', "OrderController@createreorder")->middleware("permission");
        Route::get('/find_drivers/{is_trip}/{package?}/{duration?}/{startDate?}/{client_id?}/{vehicle_class?}/{driver_id?}', 'OrderController@findDrivers');

        Route::get('/getprices', 'OrderController@getprices');
        Route::post('/getprices', 'OrderController@getprices');

        Route::get('/getvehicles/{passenger_count}/{client_company_id}', 'OrderController@getvehicles');
        Route::get('/getvehicles/{passenger_count}', 'OrderController@getvehicles');//bu yanlış değil silme
        Route::get('/getpassengers/{order_id}', 'OrderController@getpassengers');
        Route::get('/getextras/{order_id}', 'OrderController@getextras');
        Route::get('/getcreditcard/{client_id}', 'OrderController@getcreditcard');

        Route::get('/getclientdirections/{client_id}', "OrderController@getclientdirections")->name('common.order.getclientdirections');


       /* Route::get('/createreorder/{orderid}', "OrderController@createreorder")->name('common.order.createreorder')->middleware("permission");
        Route::post('/createreorder', "OrderController@createreorder")->middleware("permission");*/

        Route::get('/createreordernew/{orderid}', "OrderController@createreordernew")->middleware("permission")->name('common.order.createreordernew');
        Route::post('/createreordernew', "OrderController@createreordernew")->middleware("permission");


        Route::get('/getclientdirections/{client_id}', "OrderController@getclientdirections")->name('common.order.getclientdirections');
        Route::get('/getdirectiondetail/{order_location_id}', "OrderController@getdirectiondetail")->name('common.order.getdirectiondetail');

        Route::get('/getorder/{order_id}', 'OrderController@getorder');

        Route::get('/updateform/{job_id}', 'OrderController@updateform')->middleware("permission");
        Route::post('/updateform', 'OrderController@updateform')->middleware("permission");

        Route::post('/braintreepayment', 'OrderController@braintreepayment');
        Route::post('/sofortpayment', 'OrderController@sofortpayment');
        Route::get('/getclientinvoicetype', 'OrderController@getclientinvoicetype');


    });


    Route::group(["middleware" => "role:client"], function () {
    });
    Route::group(["middleware" => "role:manager|operator"], function () {
    });


});

