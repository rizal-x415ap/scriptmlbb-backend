<?php

use App\Http\Controllers\Api\v1\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dynamic robots.txt, ads.txt, sitemap.xml & rss.xml plain text / XML endpoints
Route::get('/robots.txt', [SettingController::class, 'robotsTxt']);
Route::get('/ads.txt', [SettingController::class, 'adsTxt']);
Route::get('/sitemap.xml', [SettingController::class, 'sitemapXml']);
Route::get('/rss.xml', [SettingController::class, 'rssXml']);
Route::get('/feed', [SettingController::class, 'rssXml']);
