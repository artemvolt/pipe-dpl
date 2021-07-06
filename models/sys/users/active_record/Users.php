<?php
declare(strict_types = 1);

namespace app\models\sys\users\active_record;

use app\components\db\ActiveRecordTrait;
use app\models\managers\Managers;
use app\models\phones\PhoneNumberValidator;
use app\models\phones\Phones;
use app\models\sys\users\active_record\relations\RelUsersToPhones;
use app\modules\history\behaviors\HistoryBehavior;
use pozitronik\helpers\DateHelper;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sys_users".
 *
 * @property int $id
 * @property string $username Отображаемое имя пользователя
 * @property string $login Логин
 * @property string $password Хеш пароля либо сам пароль (если $salt пустой)
 * @property null|string $restore_code Код восстановления пароля, если запрошен
 * @property null|string $salt Unique random salt hash
 * @property bool $is_pwd_outdated Пароль должен быть сменён пользователем
 * @property string $email email
 * @property string $comment Служебный комментарий пользователя
 * @property string $create_date Дата регистрации
 * @property int $daddy ID зарегистрировавшего/проверившего пользователя
 * @property bool $deleted Флаг удаления
 *
 * @property-read UsersTokens[] $relatedUsersTokens Связанные с моделью пользователя модели токенов
 * @property RelUsersToPhones[] $relatedUsersToPhones Связь к промежуточной таблице к телефонным номерам
 * @property Phones[] $relatedPhones Телефонные номера пользователя (таблица)
 * @property string[] $phones Виртуальный атрибут: телефонные номера в строковом массиве, используется для редактирования
 * @property Managers $manager
 */
class Users extends ActiveRecord {
	use ActiveRecordTrait;

	private ?array $_phones = null;

	/**
	 * @inheritDoc
	 */
	public function behaviors():array {
		return [
			'history' => [
				'class' => HistoryBehavior::class
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'sys_users';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['username', 'login', 'password', 'email'], 'required'],//Не ставим create_date как required, поле заполнится default-валидатором (а если нет - отвалится при инсерте в базу)
			[['comment'], 'string'],
			[['create_date'], 'safe'],
			[['daddy'], 'integer'],
			[['deleted', 'is_pwd_outdated'], 'boolean'],
			[['deleted', 'is_pwd_outdated'], 'default', 'value' => false],
			[['username', 'password', 'salt', 'email'], 'string', 'max' => 255],
			[['restore_code'], 'string', 'max' => 40],
			[['login'], 'string', 'max' => 64],
			[['login'], 'unique'],
			[['email'], 'unique'],
			[['daddy'], 'default', 'value' => Yii::$app->user->id],
			[['create_date'], 'default', 'value' => DateHelper::lcDate()],//default-валидатор срабатывает только на незаполненные атрибуты, его нельзя использовать как обработчик любых изменений атрибута
			['phones', PhoneNumberValidator::class, 'when' => function() {
				[] !== array_filter($this->phones);
			}],
			['relatedPhones', 'safe']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'username' => 'Имя пользователя',
			'login' => 'Логин',
			'password' => 'Пароль',
			'restore_code' => 'Код восстановления',
			'salt' => 'Соль',
			'is_pwd_outdated' => 'Пользователь должен сменить пароль при входе',
			'email' => 'Почтовый адрес',
			'comment' => 'Служебный комментарий пользователя',
			'create_date' => 'Дата регистрации',
			'daddy' => 'ID зарегистрировавшего/проверившего пользователя',
			'deleted' => 'Флаг удаления',
			'update_password' => 'Новый пароль'
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsersTokens():ActiveQuery {
		return $this->hasMany(UsersTokens::class, ['user_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedUsersToPhones():ActiveQuery {
		return $this->hasMany(RelUsersToPhones::class, ['user_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getRelatedPhones():ActiveQuery {
		return $this->hasMany(Phones::class, ['id' => 'phone_id'])->via('relatedUsersToPhones');
	}

	/**
	 * @param mixed $relatedPhones
	 * @throws Throwable
	 */
	public function setRelatedPhones($relatedPhones):void {
		RelUsersToPhones::linkModels($this, $relatedPhones);
	}

	/**
	 * @return string[]
	 */
	public function getPhones():array {
		if (null === $this->_phones) {
			$this->_phones = ArrayHelper::getColumn($this->relatedPhones, 'phone');
		}
		return $this->_phones;
	}

	/**
	 * @param mixed $phones
	 */
	public function setPhones($phones):void {
		$this->_phones = (array)$phones;
	}

	/**
	 * @inheritDoc
	 */
	public function save($runValidation = true, $attributeNames = null):bool {
		if (true === $saved = parent::save($runValidation, $attributeNames)) {
			/*
			 * Это не очень красиво, и я предполагал сделать это через релейшен-атрибуты, проверяемые в
			 * \app\components\db\ActiveRecordTrait::createModel(mappedParams)
			 * Вышло так, пусть будет. По крайней мере, выглядит логично.
			*/
			$this->relatedPhones = Phones::add($this->_phones);
		}
		return $saved;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getManager():ActiveQuery {
		return $this->hasOne(Managers::class, ['user_id' => 'id']);
	}
}
