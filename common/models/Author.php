<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "author".
 *
 * @property int $id
 * @property string $fio
 *
 * @property BookAuthor[] $bookAuthors
 * @property Sub[] $subs
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio'], 'required'],
            [['fio'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'ФИО',
        ];
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['author_id' => 'id']);
    }
    
    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this
            ->hasMany(Book::class, ['id' => 'book_id'])
            ->via('bookAuthors');
    }

    /**
     * query книг за определенный год
     * @param mixed $year
     * @return Yii\db\ActiveQuery
     */
    public function getBooksYear($year)
    {
        return $this->getBooks()->where(['book.year' => $year]);
    }
    
    /**
     * Gets query for [[Subs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubs()
    {
        return $this->hasMany(Sub::class, ['author_id' => 'id']);
    }

    /**
     * Ссылка на детальную страницу
     * @return string
     */
    public function getUrl() {
        return Url::to(['author/view', 'id' => $this->id]);
    }

    /**
     * Кликабельное название на детальную страницу автора
     * @return string
     */
    public function getTitleLink()
    {
        return Html::a(
            $this->fio,
            $this->getUrl(),
            [
                'title' => $this->fio,
            ]
        );
    }

    /**
     * Топ 10 авторов по количеству книг за $year
     * @param string $year
     * @return Yii\db\ActiveQuery
     */
    public static function findTopAuthors($year)
    {
        if ($year === null) {
            $year = date('Y');
        }

        return self::find()
            ->select([
                'author.id',
                'author.fio',
                'COUNT(book_author.book_id) AS book_count'
            ])
            ->joinWith('books')
            ->where(['book.year' => $year])
            ->groupBy('author.id')
            ->orderBy(['book_count' => SORT_DESC])
            ->limit(10);
    }
}
