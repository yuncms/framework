<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `trade_refunds`.
 */
class m180315_104659_create_trade_refunds_table extends Migration
{
    public $tableName = '{{%trade_refunds}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id' => $this->bigPrimaryKey(),
            'amount' => $this->unsignedInteger()->notNull(),//退款金额大于 0, 单位为对应币种的最小货币单位，例如：人民币为分（如退款金额为 1 元，此处请填 100）。必须小于等于可退款金额，默认为全额退款。
            'succeed' => $this->boolean()->defaultValue(false),//退款是否成功。
            'status' => $this->string(10)->defaultValue('pending'),//退款状态（目前支持三种状态: pending: 处理中; succeeded: 成功; failed: 失败）。
            'time_succeed' => $this->unixTimestamp(),//退款成功的时间，用 Unix 时间戳表示。
            'description' => $this->string(255)->notNull(),//退款详情，最多 255 个 Unicode 字符。
            'failure_code' => $this->string(),//退款的错误码，详见 错误 中的错误码。
            'failure_msg' => $this->string(),//退款消息的描述。
            //'metadata',
            'charge_id' => $this->unsignedInteger()->notNull(),//支付  charge 对象的  id
            'charge_order_no' => $this->string(64),//商户订单号，这边返回的是  charge 对象中的  order_no 参数。
            'transaction_no' => $this->string(64),//支付渠道返回的交易流水号，部分渠道返回该字段为空。
            'funding_source' => $this->string(20),//微信及 QQ 类退款资金来源。取值范围： unsettled_funds ：使用未结算资金退款； recharge_funds ：微信-使用可用余额退款，QQ-使用可用现金账户资金退款。注：默认值  unsettled_funds ，该参数对于微信渠道的退款来说仅适用于微信老资金流商户使用，包括  wx 、 wx_pub 、 wx_pub_qr 、 wx_lite 、 wx_wap 、 wx_pub_scan 六个渠道；新资金流退款资金默认从基本账户中扣除。该参数仅在请求退款，传入该字段时返回。
            //'extra',
            'created_at' => $this->unixTimestamp(),
        ], $tableOptions);

        $this->addForeignKey('trade_refunds_fk_1', $this->tableName, 'charge_id', '{{%trade_charges}}', 'id', 'CASCADE', 'RESTRICT');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}