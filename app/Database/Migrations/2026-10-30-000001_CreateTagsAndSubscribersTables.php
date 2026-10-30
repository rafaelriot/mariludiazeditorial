<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Esquema para Etiquetas (Tags), relación Post-Tags y Suscriptores de Newsletter.
 */
class CreateTagsAndSubscribersTables extends Migration
{
    public function up(): void
    {
        // -----------------------------------------------------------------
        // tags
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 80],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('tags', true);

        // -----------------------------------------------------------------
        // post_tags (relación muchos a muchos)
        // -----------------------------------------------------------------
        $this->forge->addField([
            'post_id' => ['type' => 'INT', 'unsigned' => true],
            'tag_id'  => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey(['post_id', 'tag_id']);
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('post_tags', true);

        // -----------------------------------------------------------------
        // subscribers (Newsletter)
        // -----------------------------------------------------------------
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'email'        => ['type' => 'VARCHAR', 'constraint' => 190],
            'token'        => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'status'       => ['type' => 'ENUM', 'constraint' => ['active', 'unconfirmed', 'unsubscribed'], 'default' => 'active'],
            'source'       => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'feed'],
            'confirmed_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('subscribers', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('subscribers', true);
        $this->forge->dropTable('post_tags', true);
        $this->forge->dropTable('tags', true);
    }
}

