<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string $isbn
 * @property int|null $file_id
 *
 * @property BookAuthor[] $bookAuthors
 * @property File $file
 * @property Author[] $authors
 */
class Book extends \yii\db\ActiveRecord
{
    public $uploadedFile;
    public $bookAuthorIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'year', 'isbn'], 'required'],
            [['year', 'file_id'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 50],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['file_id' => 'id']],
            [['bookAuthorIds'], 'each', 'rule' => ['exist', 'targetClass' => Author::class, 'targetAttribute' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'Isbn',
            'file_id' => 'Фото главной страницы',
            'bookAuthorIds' => 'Автор(ы)',
            'authorname' => 'Автор(ы)',
            'uploadedFile' => 'Фото главной страницы',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        foreach ($this->bookAuthors as $author_rel) {
            $this->bookAuthorIds[] = $author_rel->author->id;
        }
    }   

    /**
     * Gets query for [[Authors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this
            ->hasMany(Author::class, ['id' => 'author_id'])
            ->via('bookAuthors');
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'file_id']);
    }

    /**
     * Массив с авторами
     * @return array
     */
    public function getArrayAuthors()
    {
        $authors_rel = $this->bookAuthors;

        $authors = [];
        foreach ($authors_rel as $author_rel) {
            $authors[] = $author_rel->author->fio;
        }

        return $authors;
    }

    /**
     * Возвращает кликабельного автора книги
     * 
     * @param bool $linked кликабельное название на детальную страницу
     * @param string $target открытие ссылки в новой вкладке
     * @return string
     */
    public function showAuthor($link = false, $target = "_self")
    {
        return implode(', ', array_map(function($author_rel) use ($link, $target) {
            $content = $author_rel->author->fio;

            if ($link) {
                return Html::a(
                    $content,
                    $author_rel->author->getUrl(),
                    [
                        'data-pjax' => 0,
                        'target' => $target,
                        'title' => $content
                    ]
                );
            } else return $content;
        }, $this->bookAuthors));
    }

    /**
     * Название файла
     * @return string|null
     */
    public function getFileName()
    {
        if ($this->file) {
            return $this->file->filename;
        }
        return null;
    }
    
    /**
     * Путь к файлу
     * @return string|null
     */
    public function getFilePreview()
    {
        if ($this->file) {
            return Url::base(true) . $this->file->getPathLink();
        }
        return null;
    }

    /**
     * Иконка для открытия файла
     * @return string|null
     */
    public function getFileIcon()
    {
        if ($this->file) {
            return Html::a(
                '<svg width="35px" height="35px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M9 15L11 17L15 13M13 3H8.2C7.0799 3 6.51984 3 6.09202 3.21799C5.71569 3.40973 5.40973 3.71569 5.21799 4.09202C5 4.51984 5 5.0799 5 6.2V17.8C5 18.9201 5 19.4802 5.21799 19.908C5.40973 20.2843 5.71569 20.5903 6.09202 20.782C6.51984 21 7.0799 21 8.2 21H15.8C16.9201 21 17.4802 21 17.908 20.782C18.2843 20.5903 18.5903 20.2843 18.782 19.908C19 19.4802 19 18.9201 19 17.8V9M13 3L19 9M13 3V7.4C13 7.96005 13 8.24008 13.109 8.45399C13.2049 8.64215 13.3578 8.79513 13.546 8.89101C13.7599 9 14.0399 9 14.6 9H19" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>',
                $this->file->getPathLink(),
                [
                    'target' => "_blank",
                    'data-pjax' => 0,
                    'title' => 'Открыть фото главной страницы'
                ]
            );
        }
    }

    /**
     * Ссылка на детальную страницу
     * @return string URL
     */
    public function getUrl() {
        return Url::to(['book/view', 'id' => $this->id]);
    }

    /**
     * Кликабельное название на детальную страницу книги
     * @return string
     */
    public function getTitleLink()
    {
        return Html::a(
            $this->title,
            $this->getUrl(),
            [
                'title' => $this->title,
            ]
        );
    }
}
