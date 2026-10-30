<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss version="2.0">
    <channel>
        <title>Marilú Díaz — Historias y Liderazgo con Propósito</title>
        <link><?= base_url() ?></link>
        <description>Blog personal de Marilú Díaz sobre liderazgo consciente, estrategia y mentalidades con propósito.</description>
        <language>es</language>
        <?php foreach ($posts as $post): ?>
            <item>
                <title><?= esc($post->title ?: 'Historia de Marilú Díaz') ?></title>
                <link><?= base_url('historia/' . $post->slug) ?></link>
                <description><?= esc($post->excerpt ?: strip_tags($post->content)) ?></description>
                <pubDate><?= ($post->published_at ?? $post->created_at)->format(DateTimeInterface::RSS) ?></pubDate>
                <guid><?= base_url('historia/' . $post->slug) ?></guid>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
