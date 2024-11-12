<?php
namespace App;

use App\User;
use App\Item;
use App\EmailSenderService;
use DateTime;
use Exception;

class ToDoList {
    private User $user;
    private array $items = [];
    private $emailSender;
    private static array $userTodoLists = [];
    private bool $bypasstime = false;

    public function __construct(User $user) {
        if (!$user->isValid()) {
            throw new Exception("L'utilisateur n'est pas valide.");
        }

        if (!$user->canHaveToDoList()) {
            throw new Exception("L'utilisateur ne peut avoir qu'une seule ToDoList");
        }
        $this->user = $user;
        $user->setHasToDoList();
    }

    public function setBypasstime($bypasstime)
    {
        $this->bypasstime = $bypasstime;
    }

    public function setEmailSender(EmailSenderService $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function add(Item $item): void {
        if (count($this->items) >= 10) {
            throw new Exception("La ToDoList ne peut pas contenir plus de 10 items");
        }

        foreach ($this->items as $existingItem) {
            if ($existingItem->getName() === $item->getName()) {
                throw new Exception("Le nom de l'item doit être unique dans la ToDoList.");
            }
        }

        if (!empty($this->items)) {
            $lastItem = end($this->items);
            $interval = $lastItem->getCreatedAt()->diff($item->getCreatedAt());
            if (!$this->bypasstime && $interval->i < 30 && $interval->h === 0) {
                throw new Exception("Il faut attendre 30 minutes entre la création de deux items.");
            }
        }

        $this->items[] = $item;

        $this->save($item);

        if (count($this->items) === 8){
            if ($this->emailSender !== null) {
                $this->emailSender->sendAlmostFullEmail($this->user);
            }
        }
    }

    public function getItems(): array {
        return $this->items;
    }

    public function save(Item $item): void {}
}
