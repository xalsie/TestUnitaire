<?php
namespace App\Tests;

use App\User;
use DateTime;
use PHPUnit\Framework\TestCase;


class UserTest extends TestCase {
    public function testEmailIsValid() {
        $user = new User("test@example.com", "John", "Doe", "Password123", new DateTime('2000-01-01'));
        $this->assertTrue($user->isValid());
    }

    public function testEmailIsInvalid() {
        $user = new User("invalid-email", "John", "Doe", "Password123", new DateTime('2000-01-01'));
        $this->assertFalse($user->isValid());
    }

    public function testNameIsInvalid() {
        $user = new User("test@example.com", "", "Doe", "Password123", new DateTime('2000-01-01'));
        $this->assertFalse($user->isValid());
    }

    public function testPasswordIsInvalid() {
        $user = new User("test@example.com", "John", "Doe", "short", new DateTime('2000-01-01'));
        $this->assertFalse($user->isValid());
    }

    public function testAgeIsInvalid() {
        $user = new User("test@example.com", "John", "Doe", "Password123", new DateTime('2015-01-01'));
        $this->assertFalse($user->isValid());
    }
}
