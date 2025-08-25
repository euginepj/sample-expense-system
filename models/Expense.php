<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "expenses".
 *
 * @property int $id
 * @property int $user_id
 * @property string $amount
 * @property string $description
 * @property string $category
 * @property string $status
 * @property string|null $receipt_file
 * @property string $submission_date
 * @property string|null $review_date
 * @property int|null $reviewed_by
 */
class Expense extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const CATEGORY_TRAVEL = 'travel';
    const CATEGORY_MEALS = 'meals';
    const CATEGORY_OFFICE_SUPPLIES = 'office_supplies';
    const CATEGORY_ENTERTAINMENT = 'entertainment';
    const CATEGORY_OTHER = 'other';

    /**
     * @var UploadedFile
     */
    public $receiptFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%expenses}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'submission_date',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'description', 'category'], 'required'], // Remove user_id from required
            [['user_id', 'reviewed_by'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['description'], 'string'],
            [['submission_date', 'review_date'], 'safe'],
            [['category'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 20],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
            [['category'], 'in', 'range' => array_keys(self::getCategories())],
            [['receiptFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, pdf, doc, docx', 'maxSize' => 1024 * 1024 * 5], // 5MB
            [['receipt_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'description' => 'Description',
            'category' => 'Category',
            'status' => 'Status',
            'receipt_file' => 'Receipt File',
            'receiptFile' => 'Receipt',
            'submission_date' => 'Submission Date',
            'review_date' => 'Review Date',
            'reviewed_by' => 'Reviewed By',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public static function getCategories()
    {
        return [
            self::CATEGORY_TRAVEL => 'Travel',
            self::CATEGORY_MEALS => 'Meals',
            self::CATEGORY_OFFICE_SUPPLIES => 'Office Supplies',
            self::CATEGORY_ENTERTAINMENT => 'Entertainment',
            self::CATEGORY_OTHER => 'Other',
        ];
    }

    public function upload()
    {
        if ($this->validate() && $this->receiptFile) {
            $basePath = Yii::getAlias('@webroot/uploads/receipts');
            if (!file_exists($basePath)) {
                mkdir($basePath, 0777, true);
            }

            $fileName = uniqid() . '_' . $this->receiptFile->baseName . '.' . $this->receiptFile->extension;
            $filePath = $basePath . '/' . $fileName;

            if ($this->receiptFile->saveAs($filePath)) {
                $this->receipt_file = $fileName;
                return true;
            }
        }
        return false;
    }

    public function getReceiptUrl()
    {
        if ($this->receipt_file) {
            return Yii::getAlias('@web/uploads/receipts/') . $this->receipt_file;
        }
        return null;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->user_id = Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }

    public function search($params)
    {
        $query = Expense::find()->where(['user_id' => Yii::$app->user->id]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['submission_date' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['category' => $this->category])
            ->andFilterWhere(['status' => $this->status]);

        return $dataProvider;
    }

    public function adminSearch($params)
    {
        $query = Expense::find()->joinWith('user');

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['submission_date' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['category' => $this->category])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['user_id' => $this->user_id]);

        return $dataProvider;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}