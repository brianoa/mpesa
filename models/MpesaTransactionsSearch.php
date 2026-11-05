<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MpesaTransactions;

/**
 * MpesaTransactionsSearch represents the model behind the search form of `app\models\MpesaTransactions`.
 */
class MpesaTransactionsSearch extends MpesaTransactions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['phone_number', 'merchant_request_id', 'checkout_request_id', 'mpesa_receipt_number', 'result_code', 'result_description', 'status', 'created_at', 'updated_at'], 'safe'],
            [['amount'], 'number'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = MpesaTransactions::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'merchant_request_id', $this->merchant_request_id])
            ->andFilterWhere(['like', 'checkout_request_id', $this->checkout_request_id])
            ->andFilterWhere(['like', 'mpesa_receipt_number', $this->mpesa_receipt_number])
            ->andFilterWhere(['like', 'result_code', $this->result_code])
            ->andFilterWhere(['like', 'result_description', $this->result_description])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
