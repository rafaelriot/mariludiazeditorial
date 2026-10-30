<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

/**
 * Roles del panel editorial.
 *
 * - admin:  control total (historias, multimedia, categorías, usuarios y ajustes).
 * - editor: crea, edita y publica historias y gestiona su multimedia.
 *
 * Los visitantes NO tienen cuenta: el sitio público es de libre lectura.
 */
class AuthGroups extends ShieldAuthGroups
{
    /**
     * Grupo asignado por defecto a usuarios creados sin grupo explícito.
     */
    public string $defaultGroup = 'editor';

    /**
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'admin' => [
            'title'       => 'Administrador',
            'description' => 'Control total de la plataforma.',
        ],
        'editor' => [
            'title'       => 'Editor',
            'description' => 'Publica historias y gestiona multimedia.',
        ],
    ];

    /**
     * Permisos disponibles.
     */
    public array $permissions = [
        'admin.access'      => 'Acceder al panel administrativo',
        'posts.manage'      => 'Crear, editar y publicar historias',
        'media.manage'      => 'Subir y gestionar imágenes',
        'categories.manage' => 'Gestionar categorías',
        'users.manage'      => 'Gestionar usuarios del panel',
        'settings.manage'   => 'Editar ajustes del sitio y del perfil',
    ];

    /**
     * Matriz de permisos por grupo.
     */
    public array $matrix = [
        'admin' => [
            'admin.*',
            'posts.*',
            'media.*',
            'categories.*',
            'users.*',
            'settings.*',
        ],
        'editor' => [
            'admin.access',
            'posts.manage',
            'media.manage',
            'categories.manage',
        ],
    ];
}
