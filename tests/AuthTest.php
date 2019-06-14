<?php

declare(strict_types=1);

namespace brown\Auth;

use \PHPUnit\Framework\TestCase;
use PDO;

class AuthTests extends TestCase
{

    public $auth;
    public static $dbh;
    public static function setUpBeforeClass()
    {
        self::$dbh = new PDO(
            "mysql:host=127.0.0.1;dbname=php_auth_test",
            "root",
            "",
            [
            ]
        );
        self::$dbh->query(
            'TRUNCATE TABLE users;'
            . 'TRUNCATE TABLE users_confirmations;'
            . 'TRUNCATE TABLE users_remembered;'
            . 'TRUNCATE TABLE users_resets;'
            . 'TRUNCATE TABLE user_throttling;'
        );
    }

    protected function setUp()
    {
        $config = ['minPassword'=>4, 'minUsername'=>4];

        $this->auth = new Auth(self::$dbh, $config);
    }
        
    public function userDataProvider()
    {
        return [
            [
                'test@email.com',
                '4r}bDZw*87Fh:9,L}',
                '4r}bDZw*87Fh:9,L}',
                null,
                true
            ],
            [
                'test@email.com',
                '4r}bDZw*87Fh:9,L}',
                '4r}bDZw*87Fh:9,L}',
                null,
                false
            ],
            [
                'bademail',
                '',
                'differentpassword',
                '',
                false
            ]
        ];
    }
    /**
     *  Test register user method passes for different scenarios
     *
     *  @dataProvider userDataProvider
     */
    public function testRegisterUser(
        $email,
        $password,
        $repeatpassword,
        $username,
        $expected
    ) {
        $this->assertSame(
            $expected,
            $this->auth->registerUser(
                $email,
                $password,
                $repeatpassword,
                $username
            )
        );
    }
}
