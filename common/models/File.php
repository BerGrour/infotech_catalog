<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $filepath
 * @property string $filename
 *
 * @property Book[] $books
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['filepath', 'filename'], 'required'],
            [['filepath', 'filename'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filepath' => 'Путь к файлу',
            'filename' => 'Название файла',
        ];
    }

    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['file_id' => 'id']);
    }
    
    /**
     * Возвращает ссылку на файл
     * @return string
     */
    public function getLinkOnFile()
    {
        $content = null;
        if ($this->filepath[0] != '/') {
            $content = '/';
        }
        $content .= $this->filepath;
        return $content;
    }

    /**
     * Получение относительного пути к файлу по URL
     * @return string
     */
    public function getPathLink()
    {
        $content = "/" . $this->filepath;
        return $content;
    }
}
