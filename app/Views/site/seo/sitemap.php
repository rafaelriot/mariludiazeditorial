<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= base_url() ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= base_url('sobre-mi') ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php foreach ($posts as $post): ?>
        <url>
            <loc><?= base_url('historia/' . $post->slug) ?></loc>
            <lastmod><?= ($post->updated_at ?? $post->created_at)->toDateString() ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
    <?php endforeach; ?>
</urlset>
