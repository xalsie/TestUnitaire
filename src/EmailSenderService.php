<?php
namespace App;

class EmailSenderService {
    public function sendAlmostFullEmail(User $user): void
    {
        echo "Email envoyé à " . $user->getEmail() . " : Votre ToDoList est presque remplie.";
    }
}
