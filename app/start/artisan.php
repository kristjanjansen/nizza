<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new ConvertBase(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertForum(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertImage(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertBlog(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertNews(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertFlight(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertOffer(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertBuysell(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertTravelmate(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertExpat(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertMisc(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertEditor(new \Illuminate\Filesystem\Filesystem));
Artisan::add(new ConvertAll(new \Illuminate\Filesystem\Filesystem));