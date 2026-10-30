<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Models\CategoryModel;
use App\Models\PostModel;
use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class EditorialSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear Usuario Administrador (Marilú Díaz)
        /** @var UserModel $userModel */
        $userModel = model(UserModel::class);
        $admin = $userModel->findByCredentials(['email' => 'admin@mariludiaz.com']);

        if (! $admin) {
            $admin = new User([
                'username' => 'mariludiaz',
                'email'    => 'admin@mariludiaz.com',
                'password' => 'Marilu2026!',
                'active'   => 1,
            ]);
            $userModel->save($admin);
            $admin = $userModel->findById($userModel->getInsertID());
            $admin->addGroup('admin');
        }

        // 2. Crear Categorías
        $catModel = model(CategoryModel::class);
        $categoriesData = [
            [
                'name'        => 'Liderazgo',
                'slug'        => 'liderazgo',
                'tone'        => 'sage',
                'sort_order'  => 1,
                'description' => 'Estrategias de claridad, serenidad y dirección personal.',
            ],
            [
                'name'        => 'Estrategia',
                'slug'        => 'estrategia',
                'tone'        => 'rose',
                'sort_order'  => 2,
                'description' => 'Posicionamiento, reputación y toma de decisiones críticas.',
            ],
            [
                'name'        => 'Cultura',
                'slug'        => 'cultura',
                'tone'        => 'navy',
                'sort_order'  => 3,
                'description' => 'Equipos de alto rendimiento, empatía y rigor operativo.',
            ],
        ];

        $catMap = [];
        foreach ($categoriesData as $c) {
            $existing = $catModel->where('slug', $c['slug'])->first();
            if (! $existing) {
                $id = $catModel->insert($c);
                $catMap[$c['slug']] = $id;
            } else {
                $catMap[$c['slug']] = $existing->id;
            }
        }

        // 3. Crear Historias de Ejemplo en el Feed
        $postModel = model(PostModel::class);
        $samplePosts = [
            [
                'title'            => 'El arte de liderar con serenidad en tiempos de hiper-aceleración',
                'slug'             => 'el-arte-de-liderar-con-serenidad',
                'excerpt'          => 'Cuando todo el ecosistema exige respuestas instantáneas, la verdadera ventaja directiva radica en la pausa táctica y la calibración profunda de decisiones.',
                'content'          => '<p>Vivimos en una época donde la inmediatez se confunde habitualmente con la efectividad. Directivos y fundadores sienten la presión constante de responder al minuto, de reaccionar a cada estímulo del mercado y de comunicar antes de reflexionar.</p><p>Sin embargo, a lo largo de más de doce años acompañando a comités de dirección, he observado un patrón inalterable: los líderes con mayor longevidad estratégica son aquellos que dominan el arte de la pausa deliberada.</p><blockquote>"La serenidad no es ausencia de urgencia; es la capacidad de decidir con nitidez en medio de la tormenta."</blockquote><p>Cuando desaceleramos el juicio automático, abrimos espacio para examinar los puntos ciegos. La próxima vez que sientas la tentación de responder de inmediato a una crisis aparente, concédete cinco minutos de silencio táctico. Tu equipo agradecerá la lucidez por encima de la prisa.</p>',
                'status'           => 'published',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'category_id'      => $catMap['liderazgo'] ?? null,
                'author_id'        => $admin->id,
                'is_pinned'        => 1,
                'reading_minutes'  => 4,
                'likes_count'      => 42,
                'comments_count'   => 5,
                'views_count'      => 380,
            ],
            [
                'title'            => 'Estrategias de reputación para líderes que no temen destacar',
                'slug'             => 'estrategias-de-reputacion-para-lideres',
                'excerpt'          => 'Construir autoridad no es vanidad mediática; es diseñar un canal de influencia nítido para respaldar misiones audaces y proyectos de largo plazo.',
                'content'          => '<p>Muchas ejecutivas brillantes se resisten a la idea de "marca personal" porque asocian el concepto a la superficialidad de las redes sociales. Este es un error comprensible pero costoso.</p><p>La reputación estratégica no consiste en acumular métricas vacías, sino en asegurar que tus valores, tu visión y tu criterio sean conocidos por aquellos que toman decisiones en tu industria. La visibilidad bien gestionada es una herramienta de protección y apalancamiento.</p>',
                'status'           => 'published',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-1 day')),
                'category_id'      => $catMap['estrategia'] ?? null,
                'author_id'        => $admin->id,
                'is_pinned'        => 0,
                'reading_minutes'  => 3,
                'likes_count'      => 28,
                'comments_count'   => 2,
                'views_count'      => 210,
            ],
            [
                'title'            => 'Construyendo culturas de alta empatía sin sacrificar la excelencia',
                'slug'             => 'culturas-de-alta-empatia-sin-sacrificar-excelencia',
                'excerpt'          => 'Desarmando la falsa dicotomía entre compasión y rigor. Las organizaciones más productivas son las que dominan conversaciones difíciles a tiempo.',
                'content'          => '<p>Existe el mito persistente de que la empatía en el trabajo debilita los resultados. En realidad, ocurre exactamente lo contrario: la falta de seguridad psicológica hace que las personas oculten los errores hasta que se convierten en desastres costosos.</p><p>La empatía directiva no significa ser permisivo con la mediocridad; significa respetar la dignidad humana de las personas mientras se mantienen estándares de desempeño extraordinariamente altos.</p>',
                'status'           => 'published',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-3 days')),
                'category_id'      => $catMap['cultura'] ?? null,
                'author_id'        => $admin->id,
                'is_pinned'        => 0,
                'reading_minutes'  => 5,
                'likes_count'      => 35,
                'comments_count'   => 4,
                'views_count'      => 450,
            ],
        ];

        foreach ($samplePosts as $sp) {
            if (! $postModel->where('slug', $sp['slug'])->first()) {
                $postModel->insert($sp);
            }
        }
    }
}

