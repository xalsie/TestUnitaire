<?php
namespace App\Tests;

use App\EmailSenderService;
use App\Item;
use App\User;
use App\ToDoList;
use DateTime;
use Exception;
use ReflectionObject;
use PHPUnit\Framework\TestCase;

class ToDoListTest extends TestCase
{
    private function createValidUser()
    {
        $birthdate = new DateTime('-14 years');
        return new User("test@example.com", "John", "Doe", "Password123", $birthdate);
    }

    private function createInvalidUser()
    {
        $birthdate = new DateTime('-10 years');
        return new User("invalid-email", "", "", "pwd", $birthdate);
    }

    public function testCreateToDoListWithValidUser()
    {
        $user = $this->createValidUser();
        $todoList = new ToDoList($user);

        $this->assertInstanceOf(ToDoList::class, $todoList);
        $this->assertEmpty($todoList->getItems());
    }

    public function testCreateToDoListWithInvalidUser()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'utilisateur n'est pas valide.");

        $user = $this->createInvalidUser();
        new ToDoList($user);
    }

    public function testAddValidItem()
    {
        $user = $this->createValidUser();
        $todoList = new ToDoList($user);

        $item = new Item("Task1", "Content for Task1");
        $todoList->add($item);

        $this->assertCount(1, $todoList->getItems());
        $this->assertEquals("Task1", $todoList->getItems()[0]->getName());
    }

    public function testAddMoreThanTenItems()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("La ToDoList ne peut pas contenir plus de 10 items");

        $user = $this->createValidUser();
        $todoList = new ToDoList($user);

        $todoList->setBypasstime(true);

        for ($i = 1; $i <= 11; $i++) {
            $item = new Item("Task$i", "Content for Task$i");
            $todoList->add($item);
        }
    }

    public function testAddDuplicateItemName()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Le nom de l'item doit être unique dans la ToDoList.");

        $user = $this->createValidUser();
        $todoList = new ToDoList($user);

        $todoList->setBypasstime(true);

        $item1 = new Item("DuplicateTask", "First content");
        $todoList->add($item1);

        $item2 = new Item("DuplicateTask", "Second content");
        $todoList->add($item2);
    }

    public function testAddItemWithContentExceedingLimit()
    {
        $user = $this->createValidUser();
        $todoList = new ToDoList($user);

        $content = str_repeat("a", 1001);
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Le contenu de l'item ne doit pas dépasser 1000 caractères.");
        
        $item = new Item("Task1", $content);
    }

    public function testAddItemWithout30MinutesInterval()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Il faut attendre 30 minutes entre la création de deux items.");

        $user = $this->createValidUser();
        $todoList = new ToDoList($user);

        $item1 = new Item("Task1", "Content for Task1");
        $todoList->add($item1);

        $mockItem = $this->createMock(Item::class);
        $mockItem->method('getName')->willReturn("Task2");
        $mockItem->method('getCreatedAt')->willReturn((new DateTime())->modify('-15 minutes'));

        $todoList->add($mockItem);
    }

    public function testAddItemWith30MinutesInterval()
    {
        $user = $this->createValidUser();
        $todoList = new ToDoList($user);

        $item1 = new Item("Task1", "Content for Task1");
        $todoList->add($item1);

        $mockItem = $this->createMock(Item::class);
        $mockItem->method('getName')->willReturn("Task2");
        $mockItem->method('getCreatedAt')->willReturn((new DateTime())->modify('+31 minutes'));

        $todoList->add($mockItem);

        $this->assertCount(2, $todoList->getItems());
        $this->assertEquals("Task2", $todoList->getItems()[1]->getName());
    }

    public function testEmailSentWhenAdding8thItem()
    {
        $user = $this->createValidUser();
        
        $emailSenderMock = $this->createMock(EmailSenderService::class);
        
        $emailSenderMock->expects($this->once())
                        ->method('sendAlmostFullEmail')
                        ->with($this->equalTo($user));

        $todoList = new ToDoList($user);

        $todoList->setEmailSender($emailSenderMock);
        $todoList->setBypasstime(true);

        for ($i = 1; $i <= 8; $i++) {
            $item = new Item("Task$i", "Content for Task$i");
            $todoList->add($item);
        }
    }

    public function testSaveMethodCalledWhenAddingItem()
    {
        $user = $this->createValidUser();
        $emailSenderMock = $this->createMock(EmailSenderService::class);

        $todoListMock = $this->getMockBuilder(ToDoList::class)
                                ->setConstructorArgs([$user, $emailSenderMock])
                                ->onlyMethods(['save'])
                                ->getMock();

        $todoListMock->expects($this->once())
                        ->method('save')
                        ->with($this->isInstanceOf(Item::class))
                        ->will($this->throwException(new Exception("Méthode save non implémentée.")));

        $item = new Item("Task1", "Content for Task1");
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Méthode save non implémentée.");

        $todoListMock->add($item);
    }
}
