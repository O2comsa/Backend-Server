<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Main Landing Routes
use App\Http\Controllers\Zoom\ZoomCategoryController;
use App\Http\Controllers\Zoom\ZoomMeetingController;
use App\Http\Controllers\Zoom\ZoomMeetingNoteController;
use App\Http\Controllers\Zoom\ZoomSettingController;
use App\Http\Controllers\Zoom\ZoomWebhookController;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\ZoomController;

use App\Http\Controllers\privacyController;

use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;

Route::view('/', 'Frontend/index');

Route::get("/privacy-policy", [privacyController::class,'index']);
Route::get("/privacy-policy-en", [privacyController::class,'privacyEn']);


Route::get('login', 'Admin\Auth\AuthController@getLogin')->name('login');
// Admin Route
Route::group(['prefix' => 'sysAdmin', 'namespace' => 'Admin'], function () {

    Route::get('login', 'Auth\AuthController@getLogin')->name('admin.login');
    Route::post('admin.login', 'Auth\AuthController@loginAdmin')->name('admin.post.login');

    Route::get('logout', ['as' => 'auth.logout', 'uses' => 'Auth\AuthController@getLogout']);

    Route::get('password/remind', 'Auth\PasswordController@forgotPassword')->name('password.remind.get');
    Route::post('password/remind', 'Auth\PasswordController@sendPasswordReminder')->name('password.remind.post');
    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset')->name('password.reset.get');
    Route::post('password/reset', 'Auth\PasswordController@postReset')->name('password.reset.post');

    Route::group(['middleware' => 'auth:admin'], function () {
        /**
         * Clear Cache
         */
        Route::get('/fix', function () {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            return redirect()->route('dashboard');
        })->name('fix');

        Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');
        Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);

        /**
         * User Profile
         */

        Route::get('profile', ['as' => 'profile', 'uses' => 'ProfileController@index']);
        Route::put('profile/details/update', ['as' => 'profile.update.details', 'uses' => 'ProfileController@updateDetails']);
        Route::post('profile/avatar/update', ['as' => 'profile.update.avatar', 'uses' => 'ProfileController@updateAvatar']);
        Route::post('profile/avatar/update/external', ['as' => 'profile.update.avatar-external', 'uses' => 'ProfileController@updateAvatarExternal']);
        Route::put('profile/login-details/update', ['as' => 'profile.update.login-details', 'uses' => 'ProfileController@updateLoginDetails']);

        /**
         * Admins Management
         */

        Route::get('admins', ['as' => 'admins.list', 'uses' => 'AdminsController@index']);
        Route::get('getAdmins', ['as' => 'admins.getAdmins', 'uses' => 'AdminsController@getAdmins']);
        Route::get('admins/create', ['as' => 'admins.create', 'uses' => 'AdminsController@create']);
        Route::post('admins/create', ['as' => 'admins.store', 'uses' => 'AdminsController@store']);
        Route::get('admins/{admin}/edit', ['as' => 'admins.edit', 'uses' => 'AdminsController@edit']);
        Route::put('admins/{admin}/update/details', ['as' => 'admins.update.details', 'uses' => 'AdminsController@updateDetails']);
        Route::put('admins/{admin}/update/login-details', ['as' => 'admins.update.login-details', 'uses' => 'AdminsController@updateLoginDetails']);
        Route::post('admins/destroy', ['as' => 'admins.destroy', 'uses' => 'AdminsController@destroy']);
        Route::post('admins/{admin}/update/avatar', ['as' => 'admins.update.avatar', 'uses' => 'AdminsController@updateAvatar']);
        Route::post('admins/{admin}/update/avatar/external', ['as' => 'admins.update.avatar.external', 'uses' => 'AdminsController@updateAvatarExternal']);


        /**
         * Users Management
         */
        Route::resource('users', 'UsersController')->only(['index', 'update']);
        Route::get('getUsers', ['as' => 'users.getUsers', 'uses' => 'UsersController@getUsers']);
        Route::get('getUsers/{user_status?}', ['as' => 'users.getUsers', 'uses' => 'UsersController@getUsers'])->name('getUsersBystatus');
        Route::get('users/create', ['as' => 'users.create', 'uses' => 'UsersController@create']);
        Route::post('users/create', ['as' => 'users.store', 'uses' => 'UsersController@store']);
        Route::get('users/{user}/edit', ['as' => 'users.edit', 'uses' => 'UsersController@edit']);
        Route::put('users/{user}/update/details', ['as' => 'users.update.details', 'uses' => 'UsersController@updateDetails']);
        Route::put('users/{user}/update/login-details', ['as' => 'users.update.login-details', 'uses' => 'UsersController@updateLoginDetails']);
        Route::post('users/destroy', ['as' => 'users.destroy', 'uses' => 'UsersController@destroy']);
        Route::post('users/changeStatus', ['as' => 'users.changeStatus', 'uses' => 'UsersController@changeStatus']);

        /**
         * Settings
         */

        Route::resource('settings', 'SettingsController');
        Route::post('settings/general', ['as' => 'settings.general.update', 'uses' => 'SettingsController@updateAll']);


        Route::get('contactus', 'ContactUsController@index')->name('contactus');
        Route::get('getContactUs', 'ContactUsController@getContactUs')->name('getContactUs');
        Route::post('getContactUs/ajax', 'ContactUsController@getContactUsAjax')->name('getContactUs.ajax');
        Route::post('sendReplay', 'ContactUsController@sendReplay')->name('sendReplay');


        Route::resource('pushNotifications', 'PushNotifications');

        //// Transactions
        Route::resource('transactions', 'TransactionsController');
//        Route::get('getTransactions', 'TransactionsController@getTransactions')->name('getTransactions');
        Route::get('getTransactions/{from_date?}/{to_date?}', ['as' => 'getTransactions', 'uses' => 'TransactionsController@getTransactions']);

        /**
         * Roles & Permissions
         */

        Route::resource('role', 'RolesController');
        Route::post('role/delete', 'RolesController@delete')->name('role.delete');

        Route::post('permission/save', [
            'as' => 'permission.save',
            'uses' => 'PermissionsController@saveRolePermissions'
        ]);

        Route::resource('permission', 'PermissionsController');


        //// Hyper Pay Transactions
        Route::resource('paytabstransactions', 'PaytabsTransactionsController');
//        Route::get('getPaytabsTransactions', 'PaytabsTransactionsController@getPaytabsTransactions')->name('getPaytabsTransactions');
        Route::get('getPaytabsTransactions/{from?}/{to?}', ['as' => 'getPaytabsTransactions', 'uses' => 'PaytabsTransactionsController@getPaytabsTransactions']);

        Route::resource('articles', 'ArticlesController');
        Route::post('articles/delete', 'ArticlesController@delete')->name('articles.delete');
        Route::get('getArticles', 'ArticlesController@getArticles')->name('getArticles');


        Route::resource('courses', 'CoursesController')->except(['destroy']);
        Route::post('courses/destroy', 'CoursesController@delete')->name('courses.destroy');
        Route::get('getCourses', 'CoursesController@getCourses')->name('getCourses');

        Route::resource('dictionaries', 'DictionariesController')->except(['destroy']);
        Route::post('dictionaries/destroy', 'DictionariesController@delete')->name('dictionaries.destroy');
        Route::get('getDictionaries', 'DictionariesController@getDictionaries')->name('getDictionaries');

        Route::resource('lessons', 'LessonsController')->except(['destroy']);
        Route::post('lessons/destroy', 'LessonsController@delete')->name('lessons.destroy');
        Route::get('getLessons', 'LessonsController@getLessons')->name('getLessons');

        /*
         * banner
         */
        Route::resource('banner', 'BannerController');
        Route::post('banner/delete', 'BannerController@delete')->name('banner.delete');

        Route::resource('certificates', 'CertificateController')->except(['destroy']);
        Route::post('certificates/destroy', 'CertificateController@delete')->name('certificates.destroy');
        Route::get('getCertificates', 'CertificateController@getCertificates')->name('getCertificates');

        Route::resource('live-event', 'LiveEventController')->except(['destroy']);
        Route::post('live-event/destroy', 'LiveEventController@delete')->name('live-event.destroy');
        Route::get('getLiveEvent', 'LiveEventController@getLiveEvent')->name('getLiveEvent');

        Route::resource('live-support-request', 'LiveSupportRequestController')->except(['destroy']);
        Route::post('live-support-request/destroy', 'LiveSupportRequestController@delete')->name('live-support-request.destroy');
        Route::get('getLiveSupportRequest', 'LiveSupportRequestController@getLiveSupportRequest')->name('getLiveSupportRequest');

        Route::resource('plans', 'PlansController')->except(['destroy']);
        Route::post('plans/destroy', 'PlansController@delete')->name('plans.destroy');
        Route::get('getPlans', 'PlansController@getPlans')->name('getPlans');

        Route::view('firebase', 'firebase');

        Route::get('zoom/add', [ZoomController::class, 'generateOAuthUrl'])->name('generateOAuthUrl');

//        Route::group(['prefix' => 'zoom'], function () {
//            Route::get('zoom-calendar', [ZoomMeetingController::class, 'calendar'])->name('zoom-meetings.calendar');
//            Route::get('zoom-meetings/start-meeting/{id}', [ZoomMeetingController::class, 'startMeeting'])->name('zoom-meetings.start_meeting');
//            Route::post('zoom-meeting/update-occurrence/{id}', [ZoomMeetingController::class, 'updateOccurrence'])->name('zoom-meetings.update_occurrence');
//            Route::post('zoom-meeting/cancel-meeting', [ZoomMeetingController::class, 'cancelMeeting'])->name('zoom-meetings.cancel_meeting');
//            Route::post('zoom-meeting/end-meeting', [ZoomMeetingController::class, 'endMeeting'])->name('zoom-meetings.end_meeting');
//            Route::post('zoom-meetings/apply-quick-action', [ZoomMeetingController::class, 'applyQuickAction'])->name('zoom-meetings.apply_quick_action');
//            Route::resource('zoom-meetings', ZoomMeetingController::class);
//
//            Route::resource('zoom-categories', ZoomCategoryController::class);
//            Route::post('zoom-settings/zoom-smtp-settings/{id?}', [ZoomSettingController::class, 'updateEmailSetting'])->name('zoom-settings.zoom-smtp-settings');
//            Route::post('zoom-settings/zoom-slack-settings/{id?}', [ZoomSettingController::class, 'updateSlackSetting'])->name('zoom-settings.zoom-slack-settings');
//
//            Route::resource('zoom-settings', ZoomSettingController::class);
//            Route::resource('meeting-note', ZoomMeetingNoteController::class);
//        });



Route::get('export-transactions/{from_date}/{to_date}/{search?}', function($from_date, $to_date, $search = '') {
    
    return Excel::download(new TransactionsExport($from_date, $to_date, $search), 'transactions.xlsx');
})->name('exportTransactions');
    });
});

Route::group(['prefix' => 'zoom', 'as' => 'zoom.'], function () {
    Route::get('redirect', [ZoomController::class, 'redirectToURL'])->name('redirect-uri');
    Route::post('webhook', [ZoomWebhookController::class, 'index'])->name('zoom-webhook');
});

Route::get('/share/{shareName}/{id}', [\App\Http\Controllers\ShareController::class, 'index']);
Route::get('delete-resources', function () {
    $directory = resource_path();

    if (File::exists($directory)) {
        File::deleteDirectory($directory);
        return 'Resources folder deleted.';
    } else {
        return 'Resources folder does not exist.';
    }
});