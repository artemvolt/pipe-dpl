<?php /** @noinspection EmptyClassInspection */
declare(strict_types = 1);

namespace app\models\core;

use pozitronik\core\helpers\ControllerHelper;
use pozitronik\helpers\ArrayHelper;
use pozitronik\helpers\ReflectionHelper;
use ReflectionException;
use Throwable;
use yii\base\UnknownClassException;
use yii\data\BaseDataProvider;
use yii\web\Controller;

/**
 * Class TemporaryHelper
 * Если понадобилось быстро сделать хелперную функцию, которую пока непонятно куда - пихаем сюда, потом рефакторим
 */
class TemporaryHelper {

	public const VERBS = [
		'GET' => 'GET',
		'HEAD' => 'HEAD',
		'POST' => 'POST',
		'PUT' => 'PUT',
		'PATCH' => 'PATCH',
		'DELETE' => 'DELETE'
	];

	/**
	 * @return string[]
	 * @throws Throwable
	 */
	public static function GetControllersList(array $controllerDirs = ['@app/controllers']):array {
		$result = [];
		foreach ($controllerDirs as $controllerDir => $idPrefix) {
			$controllers = ControllerHelper::GetControllersList((string)$controllerDir, null, [Controller::class]);
			$result[$controllerDir] = ArrayHelper::map($controllers, function(Controller $model) use ($idPrefix) {
				return ('' === $idPrefix)?$model->id:$idPrefix.'/'.$model->id;
			}, function(Controller $model) use ($idPrefix) {
				return ('' === $idPrefix)?$model->id:$idPrefix.'/'.$model->id;
			});
		}
		return $result;
	}

	/**
	 * todo: перенести метод в ControllerHelper, а ControllerTrait сделать только обёртку
	 * Возвращает все экшены контроллера
	 * @param string $controller_class
	 * @param bool $asRequestName Привести имя экшена к виду в запросе
	 * @return string[]
	 * @throws ReflectionException
	 * @throws UnknownClassException
	 */
	public static function GetControllerActions(string $controller_class, bool $asRequestName = true):array {
		$names = ArrayHelper::getColumn(ReflectionHelper::GetMethods($controller_class), 'name');
		$names = preg_filter('/^action([A-Z])(\w+?)/', '$1$2', $names);
		if ($asRequestName) {
			foreach ($names as &$name) $name = self::GetActionRequestName($name);
		}
		return $names;
	}

	/**
	 * todo: перенести метод в ControllerHelper, а ControllerTrait сделать только обёртку
	 * Переводит вид имени экшена к виду запроса, который этот экшен дёргает.
	 * @param string $action
	 * @return string
	 * @example actionSomeActionName => some-action-name
	 * @example OtherActionName => other-action-name
	 */
	public static function GetActionRequestName(string $action):string {
		/** @var array $lines */
		$lines = preg_split('/(?=[A-Z])/', $action, -1, PREG_SPLIT_NO_EMPTY);
		if ('action' === $lines[0]) unset($lines[0]);
		return mb_strtolower(implode('-', $lines));
	}

	/**
	 * @param string $term
	 * @param bool $fromQWERTY
	 * @return string
	 */
	public static function SwitchKeyboard(string $term, bool $fromQWERTY = false):string {
		$converter = $fromQWERTY
			?[
				'f' => 'а', ',' => 'б', 'd' => 'в', 'u' => 'г', 'l' => 'д', 't' => 'е', '`' => 'ё',
				';' => 'ж', 'p' => 'з', 'b' => 'и', 'q' => 'й', 'r' => 'к', 'k' => 'л', 'v' => 'м',
				'y' => 'н', 'j' => 'о', 'g' => 'п', 'h' => 'р', 'c' => 'с', 'n' => 'т', 'e' => 'у',
				'a' => 'ф', '[' => 'х', 'w' => 'ц', 'x' => 'ч', 'i' => 'ш', 'o' => 'щ', 'm' => 'ь',
				's' => 'ы', ']' => 'ъ', "'" => "э", '.' => 'ю', 'z' => 'я',
				'F' => 'А', '<' => 'Б', 'D' => 'В', 'U' => 'Г', 'L' => 'Д', 'T' => 'Е', '~' => 'Ё',
				':' => 'Ж', 'P' => 'З', 'B' => 'И', 'Q' => 'Й', 'R' => 'К', 'K' => 'Л', 'V' => 'М',
				'Y' => 'Н', 'J' => 'О', 'G' => 'П', 'H' => 'Р', 'C' => 'С', 'N' => 'Т', 'E' => 'У',
				'A' => 'Ф', '{' => 'Х', 'W' => 'Ц', 'X' => 'Ч', 'I' => 'Ш', 'O' => 'Щ', 'M' => 'Ь',
				'S' => 'Ы', '}' => 'Ъ', '"' => 'Э', '>' => 'Ю', 'Z' => 'Я',
				'@' => '"', '#' => '№', '$' => ';', '^' => ':', '&' => '?', '/' => '.', '?' => ',']
			:[
				'а' => 'f', 'б' => ',', 'в' => 'd', 'г' => 'u', 'д' => 'l', 'е' => 't', 'ё' => '`',
				'ж' => ';', 'з' => 'p', 'и' => 'b', 'й' => 'q', 'к' => 'r', 'л' => 'k', 'м' => 'v',
				'н' => 'y', 'о' => 'j', 'п' => 'g', 'р' => 'h', 'с' => 'c', 'т' => 'n', 'у' => 'e',
				'ф' => 'a', 'х' => '[', 'ц' => 'w', 'ч' => 'x', 'ш' => 'i', 'щ' => 'o', 'ь' => 'm',
				'ы' => 's', 'ъ' => ']', 'э' => "'", 'ю' => '.', 'я' => 'z',
				'А' => 'F', 'Б' => '<', 'В' => 'D', 'Г' => 'U', 'Д' => 'L', 'Е' => 'T', 'Ё' => '~',
				'Ж' => ':', 'З' => 'P', 'И' => 'B', 'Й' => 'Q', 'К' => 'R', 'Л' => 'K', 'М' => 'V',
				'Н' => 'Y', 'О' => 'J', 'П' => 'G', 'Р' => 'H', 'С' => 'C', 'Т' => 'N', 'У' => 'E',
				'Ф' => 'A', 'Х' => '{', 'Ц' => 'W', 'Ч' => 'X', 'Ш' => 'I', 'Щ' => 'O', 'Ь' => 'M',
				'Ы' => 'S', 'Ъ' => '}', 'Э' => '"', 'Ю' => '>', 'Я' => 'Z',
				'"' => '@', '№' => '#', ';' => '$', ':' => '^', '?' => '&', '.' => '/', ',' => '?',
			];

		return strtr($term, $converter);
	}

	/**
	 * @param BaseDataProvider $dataProvider
	 * @return string[]
	 * @see GridView::guessColumns
	 */
	public static function GuessDataProviderColumns(BaseDataProvider $dataProvider):array {
		$columns = [];
		$models = $dataProvider->getModels();
		$model = reset($models);
		if (is_array($model) || is_object($model)) {
			foreach ($model as $name => $value) {
				if (null === $value || is_scalar($value) || is_callable([$value, '__toString'])) {
					$columns[] = (string)$name;
				}
			}
		}
		return $columns;
	}

	/**
	 * @param array $errors
	 * @param array|string $separator
	 * @return string
	 */
	public static function Errors2String(array $errors, $separator = "\n"):string {
		$output = [];
		foreach ($errors as $attribute => $attributeErrors) {
			$error = implode($separator, $attributeErrors);
			$output[] = "{$attribute}: {$error}";
		}

		return implode($separator, $output);
	}
}