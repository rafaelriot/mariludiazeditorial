<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Models\PostModel;

class Seo extends BaseController
{
    public function sitemap()
    {
        $postModel = model(PostModel::class);
        $posts = $postModel->getFeed(100, 0);

        return $this->response
            ->setContentType('application/xml')
            ->setBody(view('site/seo/sitemap', ['posts' => $posts]));
    }

    public function rss()
    {
        $postModel = model(PostModel::class);
        $posts = $postModel->getFeed(20, 0);

        return $this->response
            ->setContentType('application/rss+xml')
            ->setBody(view('site/seo/rss', ['posts' => $posts]));
    }

    public function robots()
    {
        $sitemapUrl = base_url('sitemap.xml');
        $body = "User-agent: *\nAllow: /\nDisallow: /admin/\n\nSitemap: {$sitemapUrl}\n";
        return $this->response->setContentType('text/plain')->setBody($body);
    }
}
