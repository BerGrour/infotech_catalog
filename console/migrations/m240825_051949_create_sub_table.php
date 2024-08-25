<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sub}}`.
 */
class m240825_051949_create_sub_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sub}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer(11)->notNull(),
            'phone'=> $this->string(11)->notNull(),
        ]);

        $this->createIndex('idx-sub-author_id', 'sub', 'author_id');
        $this->addForeignKey('fk-sub-author_id', 'sub', 'author_id', 'author', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropForeignKey('fk-sub-author_id','sub');
        $this->dropIndex('idx-sub-author_id','sub');

        $this->dropTable('{{%sub}}');
    }
}
