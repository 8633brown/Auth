<?php

declare(strict_types=1);

namespace brown\Auth;

class Validator
{

    private $error = false;
    private $minPassword = 5;
    private $minUsername = 5;
    /**
     *  Create instance of Auth
     *
     *  @param PDO $dbh Instance of PDO
     */
    public function __construct(array $config = [])
    {

        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Email validation method
     *
     * @param string $email Email to validate
     * @return bool false on fail
     */
    public function validateEmail(string $email)
    {
        $email = \trim($email);
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setError();
        }
        return !$this->error();
    }

    /**
     * password validation method
     *
     * @param string $password password to validate
     * @return bool false on fail
     */
    public function validatePassword(string $password)
    {
        if (strlen($password) < $this->minPassword) {
            $this->setError();
        }
        return !$this->error();
    }
    /**
     * repeatpassword validation method
     *
     * @param string $password password to validate
     * @param string $repeatpassword repeated password to validate
     * @return bool true success false fail
     */
    public function validateRepeatPassword(string $password, string $repeatpassword)
    {
        if ($password !== $repeatpassword) {
            $this->setError();
        }
        return !$this->error();
    }

    private function setError()
    {
        $this->error = true;
    }

    public function error()
    {
        return $this->error;
    }

    public function validateUsername(string $username)
    {
        if (strlen($username) < $this->minUsername) {
            $this->setError();
        }
        return !$this->error();
    }
}
