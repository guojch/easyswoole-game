<?php
/**
 * invoke模式
 * User: Siam
 * Date: 2019/12/12
 * Time: 17:57
 */

namespace EasySwoole\ORM\Tests;

use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use PHPUnit\Framework\TestCase;


use EasySwoole\ORM\Tests\models\TestUserListGetterModel;

class InvokeOrmTest  extends TestCase
{
    /**
     * @var $connection Connection
     */
    protected $connection;
    protected $tableName = 'user_test_list';

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $config           = new Config(MYSQL_CONFIG);
        $this->connection = new Connection($config);
        DbManager::getInstance()->addConnection($this->connection);
        $connection = DbManager::getInstance()->getConnection();
        $this->assertTrue($connection === $this->connection);
    }

    /**
     * @throws \Throwable
     */
    public function testAdd()
    {
        DbManager::getInstance()->invoke(function ($client) {
            $testUserModel          = TestUserListGetterModel::invoke($client);
            $testUserModel->state   = 1;
            $testUserModel->name    = 'Siam';
            $testUserModel->age     = 18;
            $testUserModel->addTime = date('Y-m-d H:i:s');
            $data                   = $testUserModel->save();
            $this->assertIsInt($data);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testGet()
    {
        DbManager::getInstance()->invoke(function ($client) {
            $testUserModel = TestUserListGetterModel::invoke($client)->get([
                'state' => 1,
                'name'  => 'Siam',
                'age'   => 18
            ]);
            $this->assertInstanceOf(TestUserListGetterModel::class, $testUserModel);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testGetVal()
    {
        DbManager::getInstance()->invoke(function ($client){
            $res = TestUserListGetterModel::invoke($client)->where([
                'state' => 1,
                'name'  => 'Siam',
                'age'   => 18
            ])->val('name');
            $this->assertEquals($res, "Siam");
        });

        DbManager::getInstance()->invoke(function ($client){
            $res = TestUserListGetterModel::invoke($client)->where([
                'state' => 1,
                'name'  => 'undefined',
                'age'   => 18
            ])->val('name');
            $this->assertNull($res);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testUpdate()
    {
        DbManager::getInstance()->invoke(function ($client){
            $testUserModel = TestUserListGetterModel::invoke($client)->get([
                'state' => 1,
                'name'  => 'Siam',
                'age'   => 18
            ]);
            $this->assertInstanceOf(TestUserListGetterModel::class, $testUserModel);
            $testUserModel->age = 28;
            $res = $testUserModel->update();
            $this->assertTrue($res);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAffairAdd()
    {
        DbManager::getInstance()->invoke(function ($client){
            $res = DbManager::getInstance()->startTransaction($client);
            $this->assertTrue($res);

            $testUserModel = TestUserListGetterModel::invoke($client);
            $testUserModel->state = 1;
            $testUserModel->name = 'Siam';
            $testUserModel->age = 22;
            $testUserModel->addTime = date('Y-m-d H:i:s');
            $data = $testUserModel->save();
            $this->assertIsInt($data);

            $res = DbManager::getInstance()->rollback($client);
            $this->assertTrue($res);

            $testUserModelGet = TestUserListGetterModel::invoke($client)->get([
                'state' => 1,
                'name'  => 'Siam',
                'age'   => 22
            ]);
            $this->assertNull($testUserModelGet);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAll()
    {
        DbManager::getInstance()->invoke(function ($client){
            $test = TestUserListGetterModel::invoke($client)->all();
            $this->assertEquals(1, count($test));
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDelete()
    {
        DbManager::getInstance()->invoke(function ($client){
            $testUserModel = TestUserListGetterModel::invoke($client);
            $res = $testUserModel->destroy(null, true);
            $this->assertIsInt($res);
        });
    }
}