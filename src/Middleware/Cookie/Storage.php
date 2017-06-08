<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware\Cookie;

use Dflydev\FigCookies\SetCookie;

interface Storage
{
    /**
     * @param SetCookie $setCookie
     */
    public function add(SetCookie $setCookie);

    /**
     * @return SetCookie[]
     */
    public function getAll();
}
