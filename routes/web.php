<?php

use Aacotroneo\Saml2\Saml2Auth;
use App\Http\Middleware\{EncryptCookies,
    VerifyCsrfToken};
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AssetsController,
    BrandingsController,
    CitiesController,
    ClientImagesController,
    ClientsController,
    CountriesController,
    HomeController,
    LanguagesController,
    ProjectsController,
    ProjectStatesController,
    ProposalsController,
    CustomAssetsController,
    Saml2Controller,
    ServicesController,
    StatesController,
    TechDocsController,
    ClientDocsController,
    ReportsController,
    OperationalDocsController,
    RatesController};

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('auth/saml2/metadata', [Saml2Controller::class, 'metadata'])->name('saml2.metadata');


Route::prefix('auth/saml2')->group(function () {
    Route::get('login', [Saml2Controller::class, 'login'])
        ->name('saml2.login');

    Route::post('callback', [Saml2Controller::class, 'acs'])
        ->name('saml2.acs')
        ->withoutMiddleware([VerifyCsrfToken::class]);

    // Agregar también la ruta GET para el callback
    Route::get('callback', [Saml2Controller::class, 'acs'])
        ->name('saml2.acs.get')
        ->withoutMiddleware([VerifyCsrfToken::class]);

    Route::get('logout', [Saml2Controller::class, 'logout'])
        ->name('saml2.logout');
});



Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

//Assets
Route::resource('assets', AssetsController::class);
Route::get('/assets/{asset}/download', [AssetsController::class, 'download'])
    ->name('assets.download');

//Brandings
Route::resource('brandings', BrandingsController::class);
Route::get('/Brandings/{branding}/download', [BrandingsController::class, 'download'])
    ->name('brandings.download');

//Client Images
Route::resource('client_images', ClientImagesController::class);
Route::get('/client_images/{client_image}/download', [ClientImagesController::class, 'download'])
    ->name('client_images.download');

//Proposals
Route::resource('proposals', ProposalsController::class);
Route::get('/proposals/{proposal}/download', [ProposalsController::class, 'download'])
    ->name('proposals.download');

//Custom Assets
Route::resource('custom_assets', CustomAssetsController::class);
Route::get('/custom_assets/{custom_asset}/download', [CustomAssetsController::class, 'download'])
    ->name('custom_assets.download');

//Tech Docs
Route::resource('tech_docs', TechDocsController::class);
Route::get('/tech_docs/{tech_doc}/download', [TechDocsController::class, 'download'])
    ->name('tech_docs.download');

//Client Docs
Route::resource('client_docs', ClientDocsController::class);
Route::get('/client_docs/{client_doc}/download', [ClientDocsController::class, 'download'])
    ->name('client_docs.download');

//Reports
Route::resource('reports', ReportsController::class);
Route::get('/reports/{report}/download', [ReportsController::class, 'download'])
    ->name('reports.download');

//Operational Docs
Route::resource('operational_docs', OperationalDocsController::class);
Route::get('/operational_docs/{operational_doc}/download', [OperationalDocsController::class, 'download'])
    ->name('operational_docs.download');

//Rates
Route::resource('rates', RatesController::class);

//Services
Route::resource('services', ServicesController::class);

//Languages
Route::resource('languages', LanguagesController::class);

//Project States
Route::resource('project_states', ProjectStatesController::class);

//Countries
Route::resource('countries', CountriesController::class);

//States
Route::resource('states', StatesController::class);
Route::get('/states/by-country/{country}', [StatesController::class, 'getByCountry'])->name('states.by-country');

//Cities
Route::resource('cities', CitiesController::class);
Route::get('/cities/by-state/{state}', [CitiesController::class, 'getByState'])->name('cities.by-state');
Route::get('cities/get/{id}', [CitiesController::class, 'getById'])->name('cities.getById');
//Route::get('/cities/get/{id}', 'CitiesController@getById')->name('cities.getById');

//Clients
Route::resource('clients', ClientsController::class);

//Projects
Route::resource('projects', ProjectsController::class);
