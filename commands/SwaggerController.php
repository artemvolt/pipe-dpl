<?php
declare(strict_types = 1);

namespace app\commands;

use app\models\core\prototypes\PHPDocParser;
use app\models\core\prototypes\SwaggerConverter;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class SwaggerController
 * @package app\commands
 */
class SwaggerController extends Controller {

	/**
	 * Конвертирует атрибуты из PHPDoc-блока описания класса в определения @SWG\Definition
	 * @param string $path
	 * @return void
	 * @throws Exception
	 */
	public function actionModel(string $path):void {
		$realPath = Yii::getAlias($path);
		if (is_dir($realPath)) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($realPath), RecursiveIteratorIterator::SELF_FIRST);
			/** @var RecursiveDirectoryIterator $file */
			foreach ($files as $file) {
				if ($file->isFile() && 'php' === $file->getExtension()) {
					$this->fromModel($file->getRealPath());
				}
			}
		} else {
			$this->fromModel($realPath);
		}

	}

	/**
	 * @param string $path
	 * @throws Exception
	 */
	private function fromModel(string $path):void {
		try {
			$fileContents = file($path);
		} /** @noinspection BadExceptionsProcessingInspection */ catch (Throwable $t) {
			$fileContents = false;
		}
		if (false !== $fileContents) {
			$parsers = [];
			$properties = PHPDocParser::ExtractAttributeLines($fileContents);
			foreach ($properties as $propertyLine) {
				$parser = new PHPDocParser();
				$parser->loadString($propertyLine);
				$parsers[] = $parser;
				//Console::output(Console::renderColoredString("%b{$parser}%n"));
			}
			$converter = new SwaggerConverter(['parsers' => $parsers]);
			if ([] !== $parsers) {
				Console::output(Console::renderColoredString("%Y{$path}:%n"));
				Console::output(Console::renderColoredString("%g{$converter}%n"));
			}

		} else {
			Console::output(Console::renderColoredString("%rФайл {$path} не найден.%n"));
		}
	}

}