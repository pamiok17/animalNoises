<?php
include 'src\index.php';
class AnimalNoisesTest extends \PHPUnit\Framework\TestCase
{
    public function testDogGetSound() {
        $dog = new Dog("Mr Pickles");
        $this->assertEquals("bark", $dog->getSound());
    }

    public function testCatGetSound() {
        $cat = new Cat("Mr Pickles");
        $this->assertEquals("meow", $cat->getSound());
    }

    public function testCowGetSound() {
        $cow = new Cow("Mr Pickles");
        $this->assertEquals("moo", $cow->getSound());
    }

    public function testUnsupportedAnimalAll() {
        $animal = new UnsupportedAnimal("Mr Pickles", "flipflop", "Sasquatch");
        $this->assertEquals("Mr Pickles", $animal->getName());
        $this->assertEquals("flipflop", $animal->getSound());
        $this->assertEquals("Sasquatch", $animal->getType());
    }
}