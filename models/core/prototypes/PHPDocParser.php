<?php
declare(strict_types = 1);

namespace app\models\core\prototypes;

use Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class PHPDocParser
 *
 * @property string $name
 * @property null|string $type
 * @property null|string $comment
 * @property bool $required
 */
class PHPDocParser extends Model {
	private const PROPERTY_REGEXP = '/(?m)@property\h*\K(?:(\S+)\h+)\$?(\S+)?(.*)$/';

	public $name;
	public ?string $type = null;
	public ?string $comment = null;
	public bool $required = false;

	/**
	 * Соответствие типов PHPDoc типам SWG. Если не указано, то совпадает
	 */
	private const TYPES_MAPPING = [
		'int' => 'integer',
		'bool' => 'boolean'
	];

	/**
	 * @inheritDoc
	 */
	public function rules():array {
		return [
			[['name', 'type', 'comment'], 'string'],
			[['name'], 'required'],
			[['required'], 'boolean'],
			[['required'], 'default', 'value' => false]
		];
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return "@property {$this->type} \${$this->name} {$this->comment}".($this->required?" (required)":"");
	}

	/**
	 * @param array $contents
	 * @return array
	 */
	public static function ExtractAttributeLines(array $contents):array {
		return preg_grep(self::PROPERTY_REGEXP, $contents);
	}

	/**
	 * @param string $typeString
	 * @param string|null $type
	 * @param bool $required
	 * @throws Exception
	 */
	private static function checkType(string $typeString, ?string &$type, bool &$required):void {
		$required = (false !== strpos($typeString, "null"));
		$splitResult = explode("|", $typeString);

		$types = array_filter($splitResult, static function($value, $key) {
			return 'null' !== $value;
		}, ARRAY_FILTER_USE_BOTH);
		$type = array_shift($types);    /*todo: что делать, если тип не строгий? Пока беру первый, дальше посмотрю*/

		$type = ArrayHelper::getValue(self::TYPES_MAPPING, $type, $type);
	}

	/**
	 * Load self from string, like
	 *    "@attribute attributeType $attributeName attribute comment"
	 * @param string $attributeString
	 * @return bool
	 * @throws Exception
	 */
	public function loadString(string $attributeString):bool {
		/** @noinspection OnlyWritesOnParameterInspection {todo} */
		/** @noinspection PhpUnusedLocalVariableInspection */
		if (false === $matchResult = preg_match(self::PROPERTY_REGEXP, $attributeString, $matches)) return false;

		self::checkType(ArrayHelper::getValue($matches, 1), $this->type, $this->required);
		$this->name = ArrayHelper::getValue($matches, 2);
		$this->comment = trim(ArrayHelper::getValue($matches, 3));

		return $this->validate();
	}

}