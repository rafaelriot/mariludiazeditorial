<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\CategoryModel;
use App\Models\PostModel;
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

        $this->assertNotNull($cat);
        $this->assertSame('sage', $cat->tone);
        $classes = $cat->getToneClasses();
        $this->assertArrayHasKey('bg', $classes);
        $this->assertArrayHasKey('text', $classes);
    }
}
