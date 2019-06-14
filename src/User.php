<?php

declare(strict_types=1);

namespace brown\Auth;

use PDO;
use PDOException;

class User
{
    protected $dbh;
    protected $previousAttributes;
    protected $attributes;

    protected function saveUser($email, $password, $username)
    {
        $q = 'INSERT INTO users
            (email, password, username, registered)
            VALUES (:email, :password, :username, :registered)';
        try {
            $stmt = $this->run($q, [
                ':email' => $email,
                ':password' => $password,
                ':username' => $username,
                ':registered' => time()
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return false;
            } else {
                throw $e;
            }
        }

        return true;

    }

    public function run($sql, $args = NULL)
    {
        $this->normalise();
        if (!$args)
        {
            return $this->dbh->query($sql);
        }
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($args);
        $this->denormalise();
        return $stmt;
    }

    private function normalise()
    {
        $this->configure($this->attributes, $this->previousAttributes);
    }

    private function denormalise()
    {
        $this->configure($this->previousAttributes, $this->attributes);
    }

    private function configure(array $newAttributes, array $oldAttributes)
    {
        foreach ($newAttributes as $key => $newValue) {
            // if the old state of the connection must be preserved
            if (isset($oldAttributes)) {
                // retrieve the old value for this attribute
                $oldValue = $this->dbh->getAttribute($key);
                // if an old value has been found
                if ($oldValue !== $newValue) {
                    // save the old value so that we're able to restore it later
                    $oldAttributes[$key] = $oldValue;
                }
            }
            // and then set the desired new value
            $this->dbh->setAttribute($key, $newValue);
        }
    }
}
