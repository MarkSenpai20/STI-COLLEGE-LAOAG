<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Output</title>
    <style>
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .info-display {
            background-color: white;
            padding: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Check if form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Create an instance of FormInfoClass
            $formInfo = new FormInfoClass();

            // Set values using setters
            $formInfo->setLastName($_POST['lastName']);
            $formInfo->setFirstName($_POST['firstName']);
            $formInfo->setMiddleInitial($_POST['middleInitial']);
            $formInfo->setAge($_POST['age']);
            $formInfo->setContactNo($_POST['contactNo']);
            $formInfo->setEmail($_POST['email']);
            $formInfo->setAddress($_POST['address']);

            // Display success message
            echo '<div class="success-message">';
            echo '<h3>Registration Successful!</h3>';
            echo '<p>Thank you for registering. Your information has been saved.</p>';
            echo '</div>';

            // Display the entered data using the displayInfo method
            echo '<div class="info-display">';
            echo $formInfo->displayInfo();
            echo '</div>';

        } else {
            echo '<div class="error-message">';
            echo '<h3>Error: No form data submitted</h3>';
            echo '<p>Please go back and fill out the registration form.</p>';
            echo '</div>';
        }
        ?>

        <a href="index.php" class="back-btn">Back to Registration Form</a>
    </div>

    <?php
    // Include the FormInfoClass definition
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
            $this->lastName = htmlspecialchars($lastName);
        }

        // Getter and Setter for First Name
        public function getFirstName() {
            return $this->firstName;
        }

        public function setFirstName($firstName) {
            $this->firstName = htmlspecialchars($firstName);
        }

        // Getter and Setter for Middle Initial
        public function getMiddleInitial() {
            return $this->middleInitial;
        }

        public function setMiddleInitial($middleInitial) {
            $this->middleInitial = htmlspecialchars($middleInitial);
        }

        // Getter and Setter for Age
        public function getAge() {
            return $this->age;
        }

        public function setAge($age) {
            $this->age = htmlspecialchars($age);
        }

        // Getter and Setter for Contact No.
        public function getContactNo() {
            return $this->contactNo;
        }

        public function setContactNo($contactNo) {
            $this->contactNo = htmlspecialchars($contactNo);
        }

        // Getter and Setter for Email
        public function getEmail() {
            return $this->email;
        }

        public function setEmail($email) {
            $this->email = htmlspecialchars($email);
        }

        // Getter and Setter for Address
        public function getAddress() {
            return $this->address;
        }

        public function setAddress($address) {
            $this->address = htmlspecialchars($address);
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