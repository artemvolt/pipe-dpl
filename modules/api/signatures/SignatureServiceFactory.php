<?php
declare(strict_types = 1);

namespace app\modules\api\signatures;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

/**
 * Class Factory
 * @package app\modules\api\signatures
 */
final class SignatureServiceFactory {
	/**
	 * @param string $app
	 * @return SignatureService
	 * @throws InvalidConfigException
	 */
	public static function build(string $app):SignatureService {
		return new SignatureService(
			Instance::ensure(ArrayHelper::getValue(Yii::$app->params, "$app.signatureOptions.signer",
				new InvalidConfigException("$app signer not set")),
				Signer::class
			),
			Instance::ensure(ArrayHelper::getValue(Yii::$app->params, "$app.signatureOptions.signerKey",
				new InvalidConfigException("$app signerKey not set")),
				Key::class
			)
		);
	}
}