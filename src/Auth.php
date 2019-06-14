<?php

declare(strict_types=1);

namespace brown\Auth;

use PDO;
use brown\Auth\Validator;
use brown\Auth\User;

class Auth extends User
{

    private $validator;

    /**
     *  Create instance of Auth
     *
     *  @param PDO $dbh Instance of PDO
     */
    public function __construct(PDO $dbh, array $config = [])
    {
        $this->dbh = $dbh;
        $this->previousAttributes = [];
        $this->attributes = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
        $this->validator = new Validator($config);
    }

    /**
     * Friendly welcome
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function registerUser(
        string $email,
        string $password,
        ?string $repeatpassword,
        ?string $username
    ):bool {
        $this->validator->validateEmail($email);
        $this->validator->validatePassword($password);
        if (isset($repeatpassword)) {
            $this->validator->validateRepeatPassword($password, $repeatpassword);
        }
        if (isset($username)) {
            $this->validator->validateUserName($username);
        }
        if ($this->validator->error()) {
            return false;
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        return $this->saveUser($email, $password, $username);
    }
}
