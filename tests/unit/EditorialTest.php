<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\SubscriberModel;
use App\Models\TagModel;
use App\Services\PostService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EditorialTest extends CIUnitTestCase
{
    public function testPostServicePurifiesHtmlAndGeneratesSlug(): void
    {
        $service = new PostService();

        $dirtyHtml = '<p>Texto legítimo <script>alert("hack")</script></p>';
        $post = $service->savePost([
            'title'   => 'Liderazgo Consciente y Estratégico',
            'content' => $dirtyHtml,
            'status'  => 'draft',
        ]);

        $this->assertNotEmpty($post->id);
        $this->assertStringNotContainsString('<script>', (string) $post->content);
        $this->assertStringContainsString('Texto legítimo', (string) $post->content);
        $this->assertSame('liderazgo-consciente-y-estrategico', $post->slug);
        $this->assertGreaterThanOrEqual(1, $post->reading_minutes);

        // Limpiar
        model(PostModel::class)->delete($post->id, true);
    }

    public function testCategoriesHaveTones(): void
    {
        $catModel = model(CategoryModel::class);
        $cat = $catModel->where('slug', 'liderazgo')->first();

        if ($cat === null) {
            $catModel->insert([
                'name'        => 'Liderazgo',
                'slug'        => 'liderazgo',
                'description' => 'Estrategia y visión',
                'tone'        => 'sage',
                'sort_order'  => 1,
            ]);
            $cat = $catModel->where('slug', 'liderazgo')->first();
        }

        $this->assertNotNull($cat);
        $this->assertSame('sage', $cat->tone);
        $classes = $cat->getToneClasses();
        $this->assertArrayHasKey('bg', $classes);
        $this->assertArrayHasKey('text', $classes);
    }

    public function testPostTagsAndRelatedPosts(): void
    {
        $catModel = model(CategoryModel::class);
        $catId = $catModel->first()?->id;
        if (! $catId) {
            $catId = (int) $catModel->insert([
                'name' => 'General',
                'slug' => 'general',
                'tone' => 'navy',
            ]);
        }

        $service = new PostService();
        $postA = $service->savePost([
            'title'       => 'Historia A sobre Innovación',
            'content'     => '<p>Contenido sobre innovación y futuro</p>',
            'category_id' => $catId,
            'status'      => 'published',
            'tags'        => 'innovacion, estrategia',
        ]);

        $postB = $service->savePost([
            'title'       => 'Historia B sobre Innovación',
            'content'     => '<p>Segundo artículo relacionado con innovación</p>',
            'category_id' => $catId,
            'status'      => 'published',
            'tags'        => 'innovacion, tecnologia',
        ]);

        $tagModel = model(TagModel::class);
        $tagIds = $tagModel->findOrCreateMany(['innovacion']);
        $this->assertNotEmpty($tagIds);
        $tag = $tagModel->find($tagIds[0]);
        $this->assertNotNull($tag);
        $this->assertSame('innovacion', $tag->slug);

        $related = model(PostModel::class)->getRelatedPosts((int) $postA->id, $catId, 2);
        $this->assertIsArray($related);

        // Cleanup
        model(PostModel::class)->delete($postA->id, true);
        model(PostModel::class)->delete($postB->id, true);
    }

    public function testSubscriberWorkflow(): void
    {
        $subModel = model(SubscriberModel::class);
        $email = 'lector.test@example.com';

        // Eliminar previo si existe
        $subModel->where('email', $email)->delete();

        $token = bin2hex(random_bytes(16));
        $id = $subModel->insert([
            'email'      => $email,
            'name'       => 'Lector Test',
            'token'      => $token,
            'status'     => 'active',
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertIsNumeric($id);

        $subscriber = $subModel->find($id);
        $this->assertNotNull($subscriber);
        $this->assertSame('active', $subscriber->status);

        // Desuscribir
        $subModel->update($id, ['status' => 'unsubscribed']);
        $subscriber = $subModel->find($id);
        $this->assertSame('unsubscribed', $subscriber->status);

        // Limpiar
        $subModel->delete($id, true);
    }
}
