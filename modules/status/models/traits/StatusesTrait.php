<?php
declare(strict_types = 1);

namespace app\modules\status\models\traits;

use app\models\sys\users\Users;
use app\modules\status\models\Status;
use app\modules\status\models\StatusModel;
use app\modules\status\models\StatusRulesModel;
use pozitronik\helpers\ReflectionHelper;
use ReflectionException;
use Throwable;
use yii\base\InvalidConfigException;
use yii\base\UnknownClassException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Trait StatusesTrait
 *
 * @property-read StatusModel[] $nextStatuses Массив статусов для следующего шага, доступных для выбора
 * @property-read StatusModel[] $availableStatuses Массив всех статусов, доступных для выбора (следующий шаг + текущий).
 * @property-read StatusModel[] $allStatuses Массив всех статусов.
 * @property-read int[] $nextStatusesId Массив id статусов для следующего шага.
 * @property-read null|StatusModel $currentStatus Модель текущего статуса
 * @property null|int $currentStatusId id текущего статуса цели
 * @property-read string $className
 *
 * @property-read null|Status $relStatus Релейшен к таблице статусов. Используем только для чтения, для записи обращаемся к Status::setCurrentStatus - там учитываются создатель и делегат. Если они не будут нужны, можно и через свойство.
 */
trait StatusesTrait {
	private $_className;

	/**
	 * @return string
	 * @throws ReflectionException
	 * @throws UnknownClassException
	 */
	public function getClassName():string {
		if (null === $this->_className) {
			$this->_className = ReflectionHelper::New($this)->name;
		}
		return $this->_className;
	}

	/**
	 * @return StatusModel[]
	 * @throws Throwable
	 */
	public function getNextStatuses():array {
		$fwdStatuses = (null === $nextStatuses = StatusRulesModel::getNextStatuses($this->className, $this->currentStatus))?$this->allStatuses:$nextStatuses;

		return array_filter($fwdStatuses, function(StatusModel $statusModel) {
			/** @var ActiveRecord $this */
			return $statusModel->isAllowed($this, Users::Current());
		}, ARRAY_FILTER_USE_BOTH);
	}

	/**
	 * @return int[]
	 */
	public function getNextStatusesId():array {
		return (null === $currentStatus = $this->currentStatus)?[]:$currentStatus->next;
	}

	/**
	 * @return null|int
	 * @throws Throwable
	 */
	public function getCurrentStatusId():?int {
		if (null === $status = $this->relStatus) {
			if (null === $currentStatus = StatusRulesModel::getInitialStatus($this->className)) {
				return null;
			}
			return $currentStatus->id;
		}
		return $status->status;
	}

	/**
	 * @param int $status
	 * @return null|bool null если присвоение отложено
	 * @throws Throwable
	 *
	 * Не совсем красиво, что присвоение статуса проксифицируется сюда (дублируются проверки), но пока не заморачиваюсь.
	 */
	public function setCurrentStatusId(int $status):?bool {
		if (in_array($status, ArrayHelper::getColumn($this->availableStatuses, 'id'), true)) {
			/** @var ActiveRecord $this */
			$this->on(ActiveRecord::EVENT_AFTER_UPDATE, function($event) {//отложим связывание после сохранения
				return Status::setCurrentStatus($event->data[0], $event->data[1]);
			}, [$this, $status]);
			$this->on(ActiveRecord::EVENT_AFTER_INSERT, function($event) {//отложим связывание после сохранения
				return Status::setCurrentStatus($event->data[0], $event->data[1]);
			}, [$this, $status]);
			return null;
		}
		/** @var ActiveRecord $this */
		$this->addError('currentStatusId', 'Нет права на изменение статуса.');
		return false;
	}

	/**
	 * @return StatusModel|null
	 * @throws Throwable
	 */
	public function getCurrentStatus():?StatusModel {
		if (null === $status = $this->relStatus) {
			if (null === $currentStatus = StatusRulesModel::getInitialStatus($this->className)) {
				return null;
			}
			return $currentStatus;
		}
		return StatusRulesModel::getStatus($this->className, $status->status);
	}

	/**
	 * @param StatusModel $currentStatus
	 * @throws Throwable
	 * @throws InvalidConfigException
	 * @throws StaleObjectException
	 */
	public function setCurrentStatus(StatusModel $currentStatus):void {
		if (in_array($currentStatus, $this->availableStatuses)) {
			$this->setCurrentStatusId($currentStatus->id);
			return;
		}
		/** @var ActiveRecord $this */
		$this->addError('currentStatus', 'Нет права на изменение статуса.');
	}

	/**
	 * @return StatusModel[]
	 */
	public function getAvailableStatuses():array {
		$fwdStatuses = $this->nextStatuses;
		array_unshift($fwdStatuses, $this->currentStatus);

		return $fwdStatuses;
	}

	/**
	 * @return StatusModel[]
	 * @throws Throwable
	 */
	public function getAllStatuses():array {
		return StatusRulesModel::getAllStatuses($this->className);
	}

	/**
	 * @return ActiveQuery
	 * Status::getCurrentStatus работает альтернативно, но вариант с релейшеном корректнее архитектурно
	 */
	public function getRelStatus():ActiveQuery {
		/** @var ActiveRecord|self $this */
		return $this->hasOne(Status::class, [
			'model_key' => 'id'
		])->andOnCondition(['model_name' => $this->className]);
	}

}