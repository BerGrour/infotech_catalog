<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_author}}`.
 */
class m240824_051206_create_book_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_author}}', [
            'id' => $this->primaryKey(),
            'book_id' => $this->integer(11)->notNull(),
            'author_id' => $this->integer(11)->notNull(),
        ]);

        $this->createIndex('idx-book_author-book_id', 'book_author', 'book_id');
        $this->addForeignKey('fk-book_author-book_id', 'book_author', 'book_id', 'book', 'id', 'CASCADE');

        $this->createIndex('idx-book_author-author_id', 'book_author', 'author_id');
        $this->addForeignKey('fk-book_author-author_id', 'book_author', 'author_id', 'author', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-book_author-book_id','book_author');
        $this->dropIndex('idx-book_author-book_id','book_author');

        $this->dropForeignKey('fk-book_author-author_id','book_author');
        $this->dropIndex('idx-book_author-author_id','book_author');

        $this->dropTable('{{%book_author}}');
    }
}
