<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Registration Form</title>
    <link rel="stylesheet" href="https://marksenpai20.github.io/bootstrap-mj-studio/prefinalsIndex.css">
   
</head>
<body>
    <div class="container">
        <h2>Online Registration Form</h2>
        <form action="display.php" method="POST">
            <div class="form-group">
                <label for="lastName">Last Name <span class="required">*</span></label>
                <input type="text" id="lastName" name="lastName" required>
            </div>

            <div class="form-group">
                <label for="firstName">First Name <span class="required">*</span></label>
                <input type="text" id="firstName" name="firstName" required>
            </div>

            <div class="form-group">
                <label for="middleInitial">Middle Initial</label>
                <input type="text" id="middleInitial" name="middleInitial" maxlength="1">
            </div>

            <div class="form-group">
                <label for="age">Age <span class="required">*</span></label>
                <input type="number" id="age" name="age" min="1" max="120" required>
            </div>

            <div class="form-group">
                <label for="contactNo">Contact No. <span class="required">*</span></label>
                <input type="text" id="contactNo" name="contactNo" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="address">Address <span class="required">*</span></label>
                <input type="text" id="address" name="address" required>
            </div>

            <button type="submit" class="submit-btn">Submit Registration</button>
        </form>
    </div>

    <?php
    class FormInfoClass {
        // Private properties
        private $lastName;
        private $firstName;
        private $middleInitial;
        private $age;
        private $contactNo;
        private $email;
        private $address;

        // Getter and Setter for Last Name
        public function getLastName() {
            return $this->lastName;
        }

        public function setLastName($lastName) {
            $this->lastName = $lastName;
        }

        // Getter and Setter for First Name
        public function getFirstName() {
            return $this->firstName;
        }

        public function setFirstName($firstName) {
            $this->firstName = $firstName;
        }

        // Getter and Setter for Middle Initial
        public function getMiddleInitial() {
            return $this->middleInitial;
        }

        public function setMiddleInitial($middleInitial) {
            $this->middleInitial = $middleInitial;
        }

        // Getter and Setter for Age
        public function getAge() {
            return $this->age;
        }

        public function setAge($age) {
            $this->age = $age;
        }

        // Getter and Setter for Contact No.
        public function getContactNo() {
            return $this->contactNo;
        }

        public function setContactNo($contactNo) {
            $this->contactNo = $contactNo;
        }

        // Getter and Setter for Email
        public function getEmail() {
            return $this->email;
        }

        public function setEmail($email) {
            $this->email = $email;
        }

        // Getter and Setter for Address
        public function getAddress() {
            return $this->address;
        }

        public function setAddress($address) {
            $this->address = $address;
        }

        // Method to display all information
        public function displayInfo() {
            $info = "<h3>Registration Information:</h3>";
            $info .= "<p><strong>Last Name:</strong> " . $this->getLastName() . "</p>";
            $info .= "<p><strong>First Name:</strong> " . $this->getFirstName() . "</p>";
            $info .= "<p><strong>Middle Initial:</strong> " . ($this->getMiddleInitial() ?: 'N/A') . "</p>";
            $info .= "<p><strong>Age:</strong> " . $this->getAge() . "</p>";
            $info .= "<p><strong>Contact No.:</strong> " . $this->getContactNo() . "</p>";
            $info .= "<p><strong>E-mail:</strong> " . $this->getEmail() . "</p>";
            $info .= "<p><strong>Address:</strong> " . $this->getAddress() . "</p>";
            return $info;
        }
    }
    ?>
</body>
</html>