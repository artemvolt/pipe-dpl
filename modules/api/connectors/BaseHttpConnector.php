<?php
declare(strict_types = 1);

namespace app\modules\api\connectors;

use yii\base\Component;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

/**
 * Class BaseConnector
 * @package app\modules\api\connectors
 */
abstract class BaseHttpConnector extends Component {
	private Client $_httpClient;

	/**
	 * BaseHttpConnector constructor.
	 * @param array $clientOptions
	 * @param array $config
	 */
	public function __construct(array $clientOptions = [], array $config = []) {
		$this->_httpClient = new Client(array_merge($clientOptions, ['transport' => CurlTransport::class]));
		parent::__construct($config);
	}

	/**
	 * @return Client
	 */
	protected function getClient():Client {
		return $this->_httpClient;
	}
}