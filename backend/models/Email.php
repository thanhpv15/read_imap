<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "email".
 *
 * @property int $id
 * @property string $email
 * @property string $type
 * @property int $import
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Email extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'type', 'created_at', 'updated_at'], 'required'],
            [['import', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email', 'type'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['type'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'type' => 'Loại Email',
            'import' => 'Đã thử',
            'status' => 'Trạng thái',
            'created_at' => 'Ngày cập nhật',
            'updated_at' => 'Ngày cập nhật',
        ];
    }
}
