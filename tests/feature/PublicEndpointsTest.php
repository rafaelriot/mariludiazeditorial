<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class PublicEndpointsTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHomePageReturnsOk(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);
        $result->assertSee('Marilú Díaz');
    }

    public function testAboutPageReturnsOk(): void
    {
        $result = $this->get('/sobre-mi');
        $result->assertStatus(200);
        $result->assertSee('Sobre Mí');
    }

    public function testAdminRequiresAuthentication(): void
    {
        $result = $this->get('/admin');
        // Unauthenticated access redirects to login
        $result->assertRedirect();
    }

    public function testSitemapReturnsXml(): void
    {
        $result = $this->get('/sitemap.xml');
        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    }
}
