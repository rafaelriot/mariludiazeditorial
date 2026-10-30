<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Esquema editorial: categorías, multimedia, historias, galería, me gusta y comentarios.
 */
class CreateEditorialTables extends Migration
{
    public function up(): void
    {
        // -----------------------------------------------------------------
        // categories
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 80],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tone'        => ['type' => 'ENUM', 'constraint' => ['navy', 'rose', 'sage'], 'default' => 'navy'],
            'sort_order'  => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('categories', true);

        // -----------------------------------------------------------------
        // media
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'path'          => ['type' => 'VARCHAR', 'constraint' => 255, 'comment' => 'Ruta relativa a FCPATH de la variante lg'],
            'mime'          => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'image/webp'],
            'width'         => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'height'        => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'bytes'         => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'alt_text'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'caption'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'dominant_color'=> ['type' => 'CHAR', 'constraint' => 7, 'null' => true],
            'uploaded_by'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('media', true);

        // -----------------------------------------------------------------
        // media_variants
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'media_id' => ['type' => 'INT', 'unsigned' => true],
            'size'     => ['type' => 'VARCHAR', 'constraint' => 10],
            'path'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'width'    => ['type' => 'SMALLINT', 'unsigned' => true],
            'height'   => ['type' => 'SMALLINT', 'unsigned' => true],
            'bytes'    => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['media_id', 'size']);
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('media_variants', true);

        // -----------------------------------------------------------------
        // posts
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'author_id'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'category_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title'            => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'slug'             => ['type' => 'VARCHAR', 'constraint' => 200],
            'excerpt'          => ['type' => 'VARCHAR', 'constraint' => 400, 'null' => true],
            'content'          => ['type' => 'MEDIUMTEXT', 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['draft', 'scheduled', 'published'], 'default' => 'draft'],
            'published_at'     => ['type' => 'DATETIME', 'null' => true],
            'is_pinned'        => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'default' => 0],
            'reading_minutes'  => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 1],
            'likes_count'      => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'comments_count'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'views_count'      => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'meta_title'       => ['type' => 'VARCHAR', 'constraint' => 70, 'null' => true],
            'meta_description' => ['type' => 'VARCHAR', 'constraint' => 170, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['status', 'published_at', 'id']);
        $this->forge->addKey(['category_id', 'status', 'published_at']);
        $this->forge->addForeignKey('category_id', 'categories', 'id', '', 'SET NULL');
        $this->forge->createTable('posts', true);

        // -----------------------------------------------------------------
        // post_media (galería ordenada)
        // -----------------------------------------------------------------
        $this->forge->addField([
            'post_id'  => ['type' => 'INT', 'unsigned' => true],
            'media_id' => ['type' => 'INT', 'unsigned' => true],
            'position' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addPrimaryKey(['post_id', 'media_id']);
        $this->forge->addKey(['post_id', 'position']);
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('media_id', 'media', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('post_media', true);

        // -----------------------------------------------------------------
        // post_likes (admite usuario logueado o visitante)
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'post_id'      => ['type' => 'INT', 'unsigned' => true],
            'user_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'visitor_hash' => ['type' => 'CHAR', 'constraint' => 64, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['post_id', 'user_id']);
        $this->forge->addKey(['post_id', 'visitor_hash']);
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('post_likes', true);

        // -----------------------------------------------------------------
        // post_comments (comentarios con login Google o usuario Shield)
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'post_id'    => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'author_name'=> ['type' => 'VARCHAR', 'constraint' => 120],
            'author_email'=>['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'author_avatar'=>['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'content'    => ['type' => 'TEXT'],
            'status'     => ['type' => 'ENUM', 'constraint' => ['approved', 'pending', 'spam'], 'default' => 'approved'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['post_id', 'status', 'created_at']);
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('post_comments', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('post_comments', true);
        $this->forge->dropTable('post_likes', true);
        $this->forge->dropTable('post_media', true);
        $this->forge->dropTable('posts', true);
        $this->forge->dropTable('media_variants', true);
        $this->forge->dropTable('media', true);
        $this->forge->dropTable('categories', true);
    }
}
