<?php
declare(strict_types = 1);

namespace app\modules\recogdol\models;

use app\modules\recogdol\exceptions\ConfigVariableNotFoundException;
use Exception;
use yii\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;
use yii\httpclient\Exception as HttpClientException;
use yii\httpclient\Response;

/**
 * Class RecogDolAPI
 */
class RecogDolAPI {
	public const METHOD_RECOGNIZE_FULL = 1;
	public const METHOD_RECOGNIZE_SHORT = 2;

	private const METHOD_RECOGNIZE_METHODS = [
		self::METHOD_RECOGNIZE_FULL => 'api/v1/recognize/full',
		self::METHOD_RECOGNIZE_SHORT => 'api/v1/recognize/short'
	];

	/** @var Client $client */
	private $client;
	/** @var null|string|false $_sslCertificate */
	private $sslCertificate; //null - default, string - file, false - disabled

	/**
	 * RecogDolAPI constructor.
	 * @throws Exception
	 */
	public function __construct() {
		$host = ArrayHelper::getValue(Yii::$app->getModule('recogdol')->params, 'connection.host', false);
		$this->sslCertificate = ArrayHelper::getValue(Yii::$app->getModule('recogdol')->params, 'connection.sslCertificate');

		if ($host) {
			$this->client = new Client([
				'baseUrl' => $host,
				'transport' => CurlTransport::class,
				'requestConfig' => [
					'format' => Client::FORMAT_JSON,
					'headers' => [
						'accept' => 'application/json',
						'Content-Type' => 'application/json'
					]
				]
			]);
		} else {
			throw new ConfigVariableNotFoundException('connection.host variable not found in config.php');
		}
	}

	/**
	 * @param string $url
	 * @param array $file
	 * @return Response
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 */
	private function sendRequest(string $url, array $file = []):Response {
		$request = $this->client->createRequest();
		$request->method = 'POST';

		if (false === $this->sslCertificate) {
			$request->addOptions([
				'sslVerifyPeer' => false
			]);
		} elseif (is_string($this->sslCertificate)) {
			$request->addOptions([
				'sslCafile' => $this->sslCertificate
			]);
		}

		$request->url = $url;

		if (!empty($file)) {
			$request->addFile($file['fileName'], $file['filePath']);
		}

		return $request->send();
	}

	/**
	 * @param Response $response
	 * @return array
	 */
	private function parseAnswer(Response $response):array {
		if (null === $content = json_decode($response->content, true, 512, JSON_OBJECT_AS_ARRAY)) {
			$errorMessage = 'Error on decoding content from RecogDol';
		}

		return [
			'status' => $response->statusCode,
			'errorMessage' => $errorMessage??null,
			'content' => $content
		];
	}

	/**
	 * @param $recognitionType
	 * @param array $file
	 * @return array
	 * @throws HttpClientException
	 * @throws InvalidConfigException
	 */
	public function recognize($recognitionType, array $file):array {
		return $this->parseAnswer($this->sendRequest(self::METHOD_RECOGNIZE_METHODS[$recognitionType], $file));
	}

}