<?php
declare(strict_types = 1);

namespace app\modules\status\models;

use app\models\sys\users\Users;
use yii\base\Model;

/**
 * Class StatusModel
 *
 * @property int $id
 * @property string $name
 * @property bool $initial
 * @property bool $finishing
 * @property null|int[] $next
 * @property bool|callable $allowed {Метод проверки доступности выбора/применения статуса для пользователя, @see isAllowed. True/False - доступно/не доступно для всех}
 * @property ?string $color {цвет элементов, ассоциированных со статусом (кнопок, бейджей), null - дефолтный)
 * @property ?string $textcolor {цвет шрифта элементов, ассоциированных со статусом (кнопок, бейджей), null - дефолтный)
 * @property string $style {строка css, сгенерированная или заданная}
 */
class StatusModel extends Model {
	public $id;
	public $name;
	public $color;
	public $textcolor;
	public $initial = false;
	public $finishing = false;
	public $next = [];
	public $allowed = true;
	private $_style = [];

	/**
	 * @inheritDoc
	 */
	public function rules():array {
		return [
			[['id'], 'integer'],
			[['name', 'color', 'textcolor'], 'string'],
			[['id', 'name'], 'required'],
			[['initial', 'finishing'], 'boolean'],
			[['next', 'allowed'], 'safe']
		];
	}

	/**
	 * StatusModel constructor.
	 * @param null|int $id
	 * @param array $config
	 */
	public function __construct(?int $id = null, array $config = []) {
		parent::__construct($config);
		if (null !== $id) $this->id = $id;
	}

	/**
	 * Проверяет, доступен ли для выбора/применения статус $this в $model для пользователя $user
	 * @param Model $model
	 * @param Users $user
	 * @return bool
	 */
	public function isAllowed(Model $model, Users $user):bool {
		if (is_callable($this->allowed)) {
			return call_user_func($this->allowed, $model, $user);
		}
		return $this->allowed;
	}

	/**
	 * @return string
	 */
	public function getStyle():string {
		if (null !== $this->color) {
			$this->_style[] = "background-color: {$this->color}";
		}

		if (null !== $this->textcolor) {
			$this->_style[] = "color: {$this->textcolor}";
		}
		return implode('; ', $this->_style);

	}

	/**
	 * @param string $style
	 */
	public function setStyle(string $style):void {
		if (false === $this->_style = explode(';', $style)) $this->_style = [];
		array_walk($this->_style, 'trim');
	}
}