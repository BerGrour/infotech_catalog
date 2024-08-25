<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sub".
 *
 * @property int $id
 * @property string $author_id
 * @property int $phone
 *
 * @property Author $author
 */
class Sub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'length' => 11],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Автор',
            'phone'=> 'Телефон',
        ];
    }

    /**
     * Gets query for [[Author]].
     * 
     * @return Yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id'=> 'author_id']);
    }
}