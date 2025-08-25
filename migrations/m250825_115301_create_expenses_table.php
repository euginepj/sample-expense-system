<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expenses}}`.
 */
class m250825_115301_create_expenses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expenses}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'description' => $this->text()->notNull(),
            'category' => $this->string(50)->notNull(),
            'status' => $this->string(20)->defaultValue('pending'),
            'receipt_file' => $this->string(255)->null(),
            'submission_date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'review_date' => $this->dateTime()->null(),
            'reviewed_by' => $this->integer()->null(),
        ]);

        // Add foreign key for user_id
        $this->addForeignKey(
            'fk-expenses-user_id',
            '{{%expenses}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // Add foreign key for reviewed_by
        $this->addForeignKey(
            'fk-expenses-reviewed_by',
            '{{%expenses}}',
            'reviewed_by',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-expenses-user_id', '{{%expenses}}');
        $this->dropForeignKey('fk-expenses-reviewed_by', '{{%expenses}}');
        $this->dropTable('{{%expenses}}');
    }
}
