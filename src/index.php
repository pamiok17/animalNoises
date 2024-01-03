<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="index.php" method="get">
        Animal Name (example: "Mr Pickles"): <input type="text" name="name">
        <br>
        Animal Type (example: "cat"): <input type="text" name="animalType">
        <br>
        <input type="submit">
    </form>
    
    <?php
        $userAnimalName = $_GET["name"] ?? "";
        $userAnimalType = $_GET["animalType"] ?? "";

        if (empty($userAnimalName) || empty($userAnimalType)) {
            echo "Please enter an animal name AND an animal type!";
        } else {
            $animalName = sanitize($userAnimalName, false);
            $animalType = sanitize($userAnimalType);
            $supportedAnimals = array("dog", "cat", "cow");
            
            if (in_array($animalType, $supportedAnimals)) {
                echo "Animal supported! <br>";
                switch($animalType) {
                    case "dog":
                        $animal = new Dog($animalName);
                        break;
                    case "cat":
                        $animal = new Cat($animalName);
                        break;
                    case "cow":
                        $animal = new Cow($animalName);
                        break;
                }

            } else {
                // animal not supported
                echo "Animal not natively supported! Let's see if Wikipedia knows...<br>";
                $wikiPageUrl = 'https://en.wikipedia.org/wiki/List_of_animal_sounds';
                $curl = curl_init($wikiPageUrl);
                // option true to be completely silent with regards to the curl functions
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);

                if (curl_errno($curl)) {
                    // check for errors
                    echo 'Try again! There was an error while reaching out: ' . curl_error($curl);
                } else {
                    $dom = new DOMDocument();
                    @$dom->loadHTML($response);
                    $rows = $dom->getElementsByTagName('tr');
                    foreach ($rows as $row) {
                        $cells = $row->getElementsByTagName('td');
                        if ($cells->length==4) {
                            $wikiAnimalType = sanitize($cells->item(1)->textContent ?? "");
                            // check if exact match
                            if ($wikiAnimalType===$animalType) {
                                $wikiAnimalNoise = sanitize($cells->item(2)->textContent ?? "");
                                $animal = new UnsupportedAnimal($animalName, $wikiAnimalNoise, $wikiAnimalType);
                                break;
                            }
                        }
                    }
                }
                curl_close($curl);
            }

            if (isset($animal)) {
                echo $animal->getName() . " the " . $animal->getType() . " says " . $animal->getSound() . ".<br>";
            } else {
                echo "That animal isn't real!.. Or just not on the wiki for animal sounds.<br>";
            }
        }

        /**
         * sanitize to prevent injection and normalize. keep letters and spaces
         */
        function sanitize($str, $useLower=true) {
            $str = preg_replace('/[^A-Za-z ]/', '', $str) ?? "";
            if ($useLower) {
                return strtolower($str) ?? "";
            }
            return $str;
        }

        /**
         * Abstract class to be extended by concrete animal classes
         */
        abstract class Animal {
            protected $name;
            protected $sound;
            protected $type;

            public function __construct($name) {
                $this->name = $name;
            }

            public function getName() {
                return $this->name;
            }

            public function getSound() {
                return $this->sound;
            }

            public function getType() {
                return $this->type;
            }

            abstract public function getExtraInfo();
        }

        class Dog extends Animal {
            public function __construct($name) {
                parent::__construct($name);
                $this->name = $name;
                $this->sound = "bark";
                $this->type = "dog";
            }

            public function getExtraInfo() {
                return "Dogs are commonly knows as man's best friend!";
            }
        }

        class Cow extends Animal {
            public function __construct($name) {
                parent::__construct($name);
                $this->sound = "moo";
                $this->type = "cow";
            }

            public function getExtraInfo() {
                return "Cows can smell up to 6 miles away!";
            }
        }

        class Cat extends Animal {
            public function __construct($name) {
                parent::__construct($name);
                $this->sound = "meow";
                $this->type = "cat";
            }

            public function getExtraInfo() {
                return "House cats sleep on average 15 hours a day!";
            }
        }

        /**
         * Bare-bones generic class for unsupported animals
         */
        class UnsupportedAnimal extends Animal {
            public function __construct($name, $sound, $type) {
                parent::__construct($name);
                $this->sound = $sound;
                $this->type = $type;
            }

            public function getExtraInfo() {
                return "No extra info for unsupported animals!";
            }
        }
    ?>
</body>
</html>