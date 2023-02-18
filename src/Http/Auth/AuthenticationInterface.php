<?php

namespace Gabormakeev\GbBlogApi\Http\Auth;

use Gabormakeev\GbBlogApi\Http\Request;
use Gabormakeev\GbBlogApi\User;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}
