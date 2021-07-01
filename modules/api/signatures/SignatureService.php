<?php
declare(strict_types = 1);

namespace app\modules\api\signatures;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;

/**
 * Class SignatureService
 * @package app\modules\api\signatures
 */
class SignatureService {
	/**
	 * @var Signer компонент для подписи.
	 */
	private Signer $_signer;
	/**
	 * @var Key ключ для подписания.
	 */
	private Key $_signerKey;

	/**
	 * SignatureService constructor.
	 * @param Signer $signer
	 * @param Key $key
	 */
	public function __construct(Signer $signer, Key $key) {
		$this->_signer = $signer;
		$this->_signerKey = $key;
	}

	/**
	 * Подписанная строка.
	 * @param string $payload
	 * @return string
	 */
	public function sign(string $payload):string {
		return $this->_signer->sign($payload, $this->_signerKey);
	}
}