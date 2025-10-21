<html>
    <head>
        <title> Class and Object Demo</title>
    </head>
    <body>
        <?php
            class Formula {

                public function TotalOfSum (int $num1, int $num2) : int{
                    return $num1 + $num2;
                }
                public function TotalOfSubtract (int $num1, int $num2) : int{
                    return $num1 - $num2;
                }
            }
            ///getter and setter in PHP
            class Person{
                private $FullName;
                public function setFullName($name){
                    $this->FullName = $name;
                }
                public function getFullName(){
                    return $this->FullName;
                }
            }

            ////formula output
            $Frm = new Formula();
            $totalSum = $Frm->TotalOfSum(20,5);
            $totalSub = $Frm->TotalOfSubtract(35,6);

            echo "<h2>Output for TotalOfSum Function</h2>";
            echo "<h3> Total: ".$totalSum."</h3>";
            echo "<h2>Output for TotalOfSubtract Function</h2>";
            echo "<h3> Total: ".$totalSub."</h3>";

            ///class person
            echo "<h2> Sample Getter and Setter in PHP </h2>";
            $person = new Person();
            $person->setFullName("Juan Dela Cruz");
            $FullName = $person->getFullName();
            echo "<h3>Hello ".$FullName."</h3>";
        ?>
    </body>
</html>