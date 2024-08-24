<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m240824_050830_create_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'year'=> $this->integer(11)->notNull(),
            'description' => $this->string(),
            'isbn'=> $this->string(50)->notNull(),
            'file_id' => $this->integer(11),
        ]);

        $this->createIndex('idx-book-file_id', 'book', 'file_id');
        $this->addForeignKey('fk-book-file_id', 'book', 'file_id', 'file', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-book-file_id','book');
        $this->dropIndex('idx-book-file_id','book');

        $this->dropTable('{{%book}}');
    }
}
