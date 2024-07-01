<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\Api\LiveEventController;
use App\Http\Controllers\Api\LiveSupportController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\PlansController;

Route::group(['namespace' => 'Api'], function () {
    //Auth
    Route::get('deleteAllWithDeletedAt','AuthController@deleteAllWithDeletedAt');
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('checkEmail', 'AuthController@checkEmail');

    Route::get('banner', 'BannerController@index');


    Route::post('password/remind', 'PasswordResetController@sendPasswordReminder');
    Route::post('password/reset', 'PasswordResetController@postReset');
    Route::post('password/verify', 'PasswordResetController@verifyToken');
    Route::post('password/test', 'PasswordResetController@testFunc');

    Route::get('search', 'SearchController@index')->name('search');

    //settings
    Route::resource('settings', 'SettingsController')->names('api-settings');

    Route::middleware(\App\Http\Middleware\PublicMiddleware::class)
        ->group(function () {
            Route::resource('article', 'ArticlesController')->only(['index', 'show']);
            Route::get('latest-article', 'ArticlesController@latest')->name('latest-article');

            Route::apiResource('dictionary', 'DictionaryController')->only(['index', 'show']);

            // contact us
            Route::post('contactUs', 'ContactUsController@store');

            Route::resource('course', 'CoursesController')->only(['index', 'show']);

            Route::resource('lesson', 'LessonsController')->only(['index', 'show']);
            Route::get('lesson/view/{id}', 'LessonsController@view');

            Route::get('plans', [PlansController::class, 'index']);
            Route::apiResource('live-events', LiveEventController::class)->only(['index', 'show']);

            Route::get('live-support-status', [LiveSupportController::class, 'index']);
            Route::resource('live-support', LiveSupportController::class)->only(['store', 'show']);
        });
        
    Route::middleware(['auth:api', \App\Http\Middleware\IdentifierMiddleware::class])
        ->group(function () {
            //logout
            Route::post('logout', 'AuthController@logout');
            Route::post('deleteAccount', 'AuthController@deleteAccount');
            //register device Token
            Route::post('register-device', 'AuthController@registerDevice');
            
            // profile
            Route::get('profile', 'AuthController@profile');
            Route::post('update-profile', 'AuthController@updateProfile');
            Route::post('update-profile-picture', 'AuthController@updateProfilePicture');

            Route::get('myCourses', 'CoursesController@myCourses');
            Route::get('course/bookmark/{id}', 'CoursesController@bookmark');

            Route::post('freeCourseSubscription', 'SubscriptionController@freeCourseSubscription');

            Route::post('paidCourseSubscription', 'SubscriptionController@paidCourseSubscription');

            ## buy
            Route::post('dictionary/buy', [DictionaryController::class, 'buyDictionary']);
            Route::post('plans/buy', [PlansController::class, 'buyPlan']);
            Route::post('live-events/buy', [LiveEventController::class, 'buyEvent']);

            // set user as viewed video
            Route::get('lesson/complete/{id}', 'LessonsController@complete');
            Route::get('lesson/bookmark/{id}', 'LessonsController@bookmark');

            Route::get('article/bookmark/{id}', 'ArticlesController@bookmark');
            Route::get('dictionary/bookmark/{id}', 'DictionaryController@bookmark');

            Route::get('notifications', [NotificationsController::class, 'index']);
            Route::get('notifications/mark-all-as-read', [NotificationsController::class, 'markAllAsRead']);
            Route::get('notifications/mark-as-read/{notificationId}', [NotificationsController::class, 'markAsRead']);

            Route::get('certificates', [CertificateController::class, 'index']);

            Route::get('myDictionary', 'DictionaryController@myDictionary');
            Route::get('myPlans', [PlansController::class, 'myPlans']);

            Route::get('bookmarked/course', 'CoursesController@listBookmarked');
            Route::get('bookmarked/lesson', 'LessonsController@listBookmarked');
            Route::get('bookmarked/article', 'ArticlesController@listBookmarked');
            Route::get('bookmarked/dictionary', 'DictionaryController@listBookmarked');
        });

    Route::post('paytabs/verify_payment', 'PaytabsController@verify_payment')->name('paytabs.verify_payment');
    Route::get('paytabs/successfullyPayment', 'PaytabsController@successfully_payment')->name('paytabs.successfully_payment');
    Route::get('paytabs/failPayment', 'PaytabsController@fail_payment')->name('paytabs.fail_payment');

    Route::post('paytabs/successfullyPayment', 'PaytabsController@successfully_payment')->name('paytabs.successfully_payment.post');
    Route::post('paytabs/failPayment', 'PaytabsController@fail_payment')->name('paytabs.fail_payment.post');
});


Route::fallback(function () {
    return response([], 404);
});
