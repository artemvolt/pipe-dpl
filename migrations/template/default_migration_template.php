<?php
declare(strict_types = 1);
/**
 * This view is used by console/controllers/MigrateController.php.
 *
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */

echo "<?php\ndeclare(strict_types = 1);\n";
if (!empty($namespace)) {
	echo "\nnamespace {$namespace};\n";
}
?>
use yii\db\Migration;

/**
* Class <?= $className . "\n" ?>
*/
class <?= $className ?> extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		/*todo: обязательно пишем revert*/
	}

}
