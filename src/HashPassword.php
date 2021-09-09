<?php

namespace Example;

use Dotenv\Dotenv;

class HashPassword
{
    /**
     * @var HashPassword|null
     */
    private static $instance = null;

    /**
     * @var mixed
     */
    private $hashPassword;

    /**
     * Create new HashPassword.
     */
    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $this->hashPassword = $_ENV['HASH_PASSWORD'];
    }

    /**
     * Get singleton instance of HashPassword.
     *
     * @return HashPassword
     */
    public static function getInstance(): HashPassword
    {
        if (self::$instance === null) {
            self::$instance = new HashPassword();
        }

        return self::$instance;
    }

    /**
     * Get hash password.
     *
     * @return mixed
     */
    public function getHashPassword()
    {
        return $this->hashPassword;
    }
}
