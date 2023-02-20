<?php

namespace Gabormakeev\GbBlogApi\Http\Auth;

use Gabormakeev\GbBlogApi\Http\Request;

interface TokenAuthenticationInterface extends AuthenticationInterface
{
    public function logout(Request $request): void;
}
