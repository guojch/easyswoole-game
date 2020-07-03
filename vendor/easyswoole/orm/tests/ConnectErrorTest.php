<?php
/**
 * 连接错误
 * User: Administrator
 * Date: 2019/11/16 0016
 * Time: 10:05
 */

namespace EasySwoole\ORM\Tests;


use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\Db\MysqlPool;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\Pool\Exception\PoolEmpty;
use PHPUnit\Framework\TestCase;



class ConnectErrorTest extends TestCase
{
    private $connection;
    private $config;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $config = new Config([
            'host'          => '127.0.0.1',
            'port'          => 3306,
            'user'          => 'error',
            'password'      => 'error',
            'database'      => 'demo',
            'timeout'       => 5,
            'charset'       => 'utf8mb4',
        ]);
        $this->config = $config;
        $this->connection = new Connection($config);
        DbManager::getInstance()->addConnection($this->connection, 'error');
    }

    public function testConnect()
    {
        /** @var Connection $connection */
        $connection = DbManager::getInstance()->getConnection('error');
        $pool = new MysqlPool($this->config);

        try {
            $obj = $pool->defer(1);
        } catch (\Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
        }
    }
}