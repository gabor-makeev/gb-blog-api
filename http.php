<?php

use Gabormakeev\GbBlogApi\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER);

$parameter = $request->query('some_parameter');
$header = $request->header('Some-Header');
$path = $request->path();

echo 'Hello from PHP';
