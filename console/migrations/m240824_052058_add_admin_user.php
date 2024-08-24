<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m240824_052058_add_admin_user
 */
class m240824_052058_add_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $user = new User();
        $user->username = 'admin';
        $user->email = 'admin@example.com';
        $user->setPassword('tmp_password');
        $user->generateAuthKey();
        $user->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $user = User::findByUsername('admin');
        if ($user) {
            $user->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240824_052058_add_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
