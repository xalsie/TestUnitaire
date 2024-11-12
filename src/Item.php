<?php
namespace App;

use DateTime;
use Exception;

class Item {
    private string $name;
    private string $content;
    private DateTime $createdAt;

    public function __construct($name, $content, DateTime $createdAt = null) {
        if (strlen($content) > 1000) {
            throw new Exception("Le contenu de l'item ne doit pas dépasser 1000 caractères.");
        }

        $this->name = $name;
        $this->content = $content;
        $this->createdAt = $createdAt ?? new DateTime();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
}
