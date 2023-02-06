<?php

namespace Gabormakeev\GbBlogApi\Http\Actions;

use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}
