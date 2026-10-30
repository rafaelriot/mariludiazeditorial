<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use Config\Site;

class Pages extends BaseController
{
    public function about()
    {
        $siteConfig = config(Site::class);

        return view('site/about', [
            'site'      => $siteConfig,
            'pageTitle' => 'Sobre Mí — Marilú Díaz',
        ]);
    }
}
