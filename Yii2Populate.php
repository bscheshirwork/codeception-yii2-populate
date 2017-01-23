<?php
namespace bscheshirwork\Codeception\Module;

use Codeception\Configuration;
use Codeception\Exception\ModuleException;
use Codeception\Exception\ModuleConfigException;
use Codeception\TestInterface;

/**
 * Works with SQL database.
 *
 * Dependence: Yii2 codeception module (part: init)
 *
 * The most important function of this module is to clean a database before each test.
 * That's why this module was added to the configuration file `acceptance.suite.yml`.
 *
 * In order to have your database populated with data you need a raw SQL dump.
 * Simply put the dump in the `tests/_data` directory (by default) and specify the path in the config.
 * The next time after the database is cleared, all your data will be restored from the dump.
 * Don't forget to include `CREATE TABLE` statements in the dump.
 *
 * Also you can include `DROP TABLE` statements in the dump to cleanup it.
 *
 * ## Config
 *
 * * dump - path to database dump
 *
 * ## Difference between this and Db config
 * * cleanup: not support. You can include `DROP TABLE` statements in the dump to cleanup it.
 * * reconnect: always true (see Codeception/Module/Yii2 _after)
 *
 * ## Example
 *
 *     modules:
 *        enabled:
 *           - \bscheshirwork\Codeception\Module\Yii2Populate:
 *              dump: 'tests/_data/dump.sql'
 */
class Yii2Populate extends \Codeception\Module
{

    /**
     * @var \yii\db\Connection
     */
    protected $db;

    /**
     * @var array
     */
    protected $sql = '';

    /**
     * Initial sql dump file must be set.
     * @var array
     */
    protected $requiredFields = ['dump'];

    public function _initialize()
    {
        if ($this->config['dump']) {
            $this->readSql();
        }
    }

    /**
     * Read sql dump from file
     * @throws ModuleConfigException
     */
    private function readSql()
    {
        if (!file_exists(Configuration::projectDir() . $this->config['dump'])) {
            throw new ModuleConfigException(
                __CLASS__,
                "\nFile with dump doesn't exist.\n"
                . "Please, check path for sql file: "
                . $this->config['dump']
            );
        }

        $this->sql = file_get_contents(Configuration::projectDir() . $this->config['dump']);
    }

    /**
     * execute SQL dump
     * @throws ModuleException
     */
    protected function loadDump()
    {
        if (!$this->sql || !$this->db) {
            return;
        }
        try {
            $this->db->createCommand($this->sql)->execute();
        } catch (\PDOException $e) {
            throw new ModuleException(
                __CLASS__,
                $e->getMessage() . "\nSQL query being executed: " . $this->db->createCommand($this->sql)->getRawSql()
            );
        }
    }

    /**
     * @inheritdoc
     * @param TestInterface $test
     */
    public function _before(TestInterface $test)
    {
        try{
            $this->db = $this->getModule('Yii2')->grabComponent('db');
        } catch (\Symfony\Component\Debug\Exception\FatalThrowableError $e) {
            throw new ModuleException(
                __CLASS__,
                $e->getMessage() . "\nYii 2 module: " . $this->getModule('Yii2')->_getName()
            );
        }
        $this->loadDump();

        parent::_before($test);
    }
}
