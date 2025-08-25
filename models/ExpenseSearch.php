<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ExpenseSearch extends Expense
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'reviewed_by'], 'integer'],
            [['amount'], 'number'],
            [['description', 'category', 'status', 'receipt_file', 'submission_date', 'review_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param bool $adminMode Whether to show all expenses (admin) or only user's expenses
     * @return ActiveDataProvider
     */
    public function search($params, $adminMode = false)
    {
        $query = Expense::find()->joinWith('user');

        if (!$adminMode) {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['submission_date' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'reviewed_by' => $this->reviewed_by,
            'category' => $this->category,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
              ->andFilterWhere(['like', 'receipt_file', $this->receipt_file]);

        // Date range filtering for submission_date
        if (!empty($this->submission_date)) {
            $dates = explode(' - ', $this->submission_date);
            if (count($dates) == 2) {
                $query->andFilterWhere(['between', 'submission_date', $dates[0], $dates[1]]);
            }
        }

        // Date range filtering for review_date
        if (!empty($this->review_date)) {
            $dates = explode(' - ', $this->review_date);
            if (count($dates) == 2) {
                $query->andFilterWhere(['between', 'review_date', $dates[0], $dates[1]]);
            }
        }

        return $dataProvider;
    }
}