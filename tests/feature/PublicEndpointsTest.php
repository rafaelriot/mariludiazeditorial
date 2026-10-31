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

    public function testSearchFilterReturnsOk(): void
    {
        $result = $this->get('/?q=estrategia');
        $result->assertStatus(200);
    }

    public function testTagPageReturnsOk(): void
    {
        $tagModel = model(\App\Models\TagModel::class);
        $tagModel->findOrCreateMany(['reflexiones']);

        $result = $this->get('/etiqueta/reflexiones');
        $result->assertStatus(200);
    }

    public function testAdminRequiresAuthentication(): void
    {
        $result = $this->get('/' . admin_path());
        // Unauthenticated access redirects to login
        $result->assertRedirect();
    }

    public function testSitemapReturnsXml(): void
    {
        $result = $this->get('/sitemap.xml');
        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function testRssFeedReturnsXml(): void
    {
        $result = $this->get('/feed.xml');
        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function testNewsletterSubscriptionEndpointValidation(): void
    {
        $security = service('security');
        $token = $security->getTokenName();
        $hash = $security->getHash();

        // Petición inválida debe devolver JSON 422
        $result = $this->post('/newsletter/suscribir', [
            $token => $hash,
            'email' => 'invalido',
        ]);
        $result->assertStatus(422);
        $result->assertJSONFragment(['success' => false]);
    }
}
