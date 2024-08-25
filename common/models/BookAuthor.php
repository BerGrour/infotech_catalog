<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "book_author".
 *
 * @property int $id
 * @property int $book_id
 * @property int $author_id
 *
 * @property Author $author
 * @property Book $book
 */
class BookAuthor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id', 'author_id'], 'required'],
            [['book_id', 'author_id'], 'integer'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_id' => 'Книга',
            'author_id' => 'Автор',
        ];
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $this->sendSms();
        }
    }


    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    
    /**
     * Отправка смс-сообщений по подпискам
     * @return void
     */
    public function sendSms()
    {
        $subs = $this->author->subs;
        $smsData = [];
        $i = 1;
        foreach ($subs as $sub) {
            $smsData[] = [
                'id' => $i,
                'to' => $sub->phone,
                'text' => "У автора {$this->author->fio} опубликована новая книга."
            ];
            $i++;
        }
        Yii::$app->sms->send($smsData);
        Yii::$app->session->setFlash("success", print_r($smsData, true));
    }
}
