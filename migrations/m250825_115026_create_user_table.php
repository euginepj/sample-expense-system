<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m250825_115026_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'role' => $this->string(20)->defaultValue('employee'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Insert some sample users
        $this->batchInsert('{{%user}}', [
            'username', 'email', 'password_hash', 'auth_key', 'role'
        ], [
            [
                'admin', 
                'admin@company.com', 
                Yii::$app->security->generatePasswordHash('admin123'), 
                Yii::$app->security->generateRandomString(), 
                'admin'
            ],
            [
                'employee1', 
                'employee1@company.com', 
                Yii::$app->security->generatePasswordHash('emp123'), 
                Yii::$app->security->generateRandomString(), 
                'employee'
            ],
            [
                'employee2', 
                'employee2@company.com', 
                Yii::$app->security->generatePasswordHash('emp123'), 
                Yii::$app->security->generateRandomString(), 
                'employee'
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
