<?php
declare(strict_types = 1);
use yii\db\Migration;

/**
 * Class m210628_144251_seller_default_fields
 */
class m210628_144251_seller_default_fields extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('sellers', 'birthday', $this->date()->defaultValue(null));
		$this->alterColumn('sellers', 'passport_series', $this->string(64)->defaultValue(null));
		$this->alterColumn('sellers', 'passport_number', $this->string(64)->defaultValue(null));
		$this->alterColumn('sellers', 'keyword', $this->string(64)->defaultValue(null));
		$this->alterColumn('sellers', 'passport_whom', $this->string()->defaultValue(null));
		$this->alterColumn('sellers', 'passport_when', $this->date()->defaultValue(null));
		$this->alterColumn('sellers', 'reg_address', $this->string()->defaultValue(null));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('sellers', 'birthday', $this->date());
		$this->alterColumn('sellers', 'passport_series', $this->string(64));
		$this->alterColumn('sellers', 'passport_number', $this->string(64));
		$this->alterColumn('sellers', 'keyword', $this->string(64));
		$this->alterColumn('sellers', 'passport_whom', $this->string());
		$this->alterColumn('sellers', 'passport_when', $this->date());
		$this->alterColumn('sellers', 'reg_address', $this->string());
	}

}
