<?php
namespace App;

use DateTime;

class User {
    private $email;
    private $firstname;
    private $lastname;
    private $password;
    private $birthdate;
    private $hasToDoList = false;

    public function __construct($email, $firstname, $lastname, $password, $birthdate) {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->birthdate = $birthdate;
    }

    public function isValid() {
        return $this->isEmailValid() &&
                $this->hasValidName() &&
                $this->isPasswordValid() &&
                $this->isAgeValid();
    }

    private function isEmailValid() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function hasValidName() {
        return !empty($this->firstname) && !empty($this->lastname);
    }

    private function isPasswordValid() {
        $length = strlen($this->password);
        return $length >= 8 && $length <= 40 &&
                preg_match('/[a-z]/', $this->password) &&
                preg_match('/[A-Z]/', $this->password) &&
                preg_match('/\d/', $this->password);
    }

    private function isAgeValid() {
        $today = new DateTime();
        $birthdate = $this->birthdate;
        $age = $today->diff($birthdate)->y;
        return $age >= 13;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function canHaveToDoList() {
        return !$this->hasToDoList;
    }

    public function setHasToDoList() {
        $this->hasToDoList = true;
    }
}
