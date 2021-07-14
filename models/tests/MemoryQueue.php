<?php
declare(strict_types = 1);

namespace app\models\tests;

use Yii;
use yii\queue\JobInterface;

/**
 * Class MemoryQueue
 * @package app\models\tests
 */
class MemoryQueue {
	/**
	 * @var JobInterface[] $jobs
	 */
	protected array $jobs;

	public function push(JobInterface $job):void {
		$this->jobs[] = $job;
	}

	public function executeJobs():void {
		foreach ($this->jobs as $job) {
			$job->execute(Yii::$app->queue);
		}
	}
}