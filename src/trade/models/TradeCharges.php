<?php

namespace yuncms\trade\models;

use Yii;
use yuncms\db\ActiveRecord;

/**
 * This is the model class for table "{{%trade_charges}}".
 *
 * @property int $id
 * @property int $paid
 * @property int $refunded
 * @property int $reversed
 * @property string $channel
 * @property string $order_no
 * @property string $client_ip
 * @property int $amount
 * @property int $amount_settle
 * @property string $currency
 * @property string $subject
 * @property string $body
 * @property int $time_paid
 * @property int $time_expire
 * @property int $time_settle
 * @property string $transaction_no
 * @property int $amount_refunded
 * @property string $failure_code
 * @property string $failure_msg
 * @property string $metadata
 * @property string $description
 * @property int $created_at
 */
class TradeCharges extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%trade_charges}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['paid', 'refunded', 'reversed', 'amount', 'amount_settle', 'time_paid', 'time_expire', 'time_settle', 'amount_refunded'], 'integer'],
            [['channel', 'order_no', 'client_ip', 'amount', 'amount_settle', 'currency', 'subject', 'body', 'amount_refunded'], 'required'],
            [['metadata'], 'string'],
            [['channel'], 'string', 'max' => 50],
            [['order_no', 'failure_code', 'failure_msg', 'description'], 'string', 'max' => 255],
            [['client_ip'], 'string', 'max' => 45],
            [['currency'], 'string', 'max' => 3],
            [['subject'], 'string', 'max' => 32],
            [['body'], 'string', 'max' => 128],
            [['transaction_no'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yuncms', 'ID'),
            'paid' => Yii::t('yuncms', 'Paid'),
            'refunded' => Yii::t('yuncms', 'Refunded'),
            'reversed' => Yii::t('yuncms', 'Reversed'),
            'channel' => Yii::t('yuncms', 'Channel'),
            'order_no' => Yii::t('yuncms', 'Order No'),
            'client_ip' => Yii::t('yuncms', 'Client Ip'),
            'amount' => Yii::t('yuncms', 'Amount'),
            'amount_settle' => Yii::t('yuncms', 'Amount Settle'),
            'currency' => Yii::t('yuncms', 'Currency'),
            'subject' => Yii::t('yuncms', 'Subject'),
            'body' => Yii::t('yuncms', 'Body'),
            'time_paid' => Yii::t('yuncms', 'Time Paid'),
            'time_expire' => Yii::t('yuncms', 'Time Expire'),
            'time_settle' => Yii::t('yuncms', 'Time Settle'),
            'transaction_no' => Yii::t('yuncms', 'Transaction No'),
            'amount_refunded' => Yii::t('yuncms', 'Amount Refunded'),
            'failure_code' => Yii::t('yuncms', 'Failure Code'),
            'failure_msg' => Yii::t('yuncms', 'Failure Msg'),
            'metadata' => Yii::t('yuncms', 'Metadata'),
            'description' => Yii::t('yuncms', 'Description'),
            'created_at' => Yii::t('yuncms', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TradeChargesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TradeChargesQuery(get_called_class());
    }
}