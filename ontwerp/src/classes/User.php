<?php
    // Functie: User class met database 
    // Auteur: Thierry Chatoorang

    require_once 'config.php';

    class User{

        // Eigenschappen 
        public string $username = "";
        public string $email = "";
        private string $password = "";
        private $conn;
        
        // Constructor: maakt database connectie
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
        
        function setPassword($password){
            $this->password = $password;
        }
        
        function getPassword(){
            return $this->password;
        }

        public function showUser() {
            echo "<br>Username: $this->username<br>";
            echo "<br>Password: $this->password<br>";
            echo "<br>Email: $this->email<br>";
        }

        public function registerUser() : array {
            $errors = [];
            
            if($this->username != ""){
                // Check of user al bestaat in database
                $query = "SELECT username FROM gebruiker WHERE username = :username";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':username', $this->username);
                $stmt->execute();
                
                if($stmt->rowCount() > 0){
                    array_push($errors, "Username bestaat al.");
                } else {
                    
                    // Insert nieuwe user in database
                    $query = "INSERT INTO gebruiker (username, password, email) VALUES (:username, :password, :email)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':username', $this->username);
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':email', $this->email);
                    
                    if(!$stmt->execute()){
                        array_push($errors, "Registratie mislukt.");
                    }
                } 
            } else {
                array_push($errors, "Username is verplicht.");
            }
            
            return $errors;
        }

        function validateUser(){
            $errors = [];

            // Controle op lege username
            if (empty($this->username)){
                array_push($errors, "Invalid username");
            }
            
            // Controle op lege password
            if (empty($this->password)){
                array_push($errors, "Invalid password");
            }

            // Test username tussen 3 en 50 tekens
            if (!empty($this->username) && (strlen($this->username) < 3 || strlen($this->username) > 50)) {
                array_push($errors, "Username moet tussen 3 en 50 tekens lang zijn");
            }
            
            return $errors;
        }

        public function loginUser(): bool {
            // Zoek user in de tabel gebruiker
            $query = "SELECT * FROM gebruiker WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $this->username);
            $stmt->execute();
            
            if($stmt->rowCount() > 0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verifieer het wachtwoord
                if(password_verify($this->password, $row['password'])){
                    // Start session en sla user op
                    session_start();
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['user_id'] = $row['id'];
                    
                    return true;
                }
            }
            
            return false;
        }

        // Check if the user is already logged in
        public function isLoggedin(): bool {
            if(!isset($_SESSION)){
                session_start();
            }
            
            if(isset($_SESSION['username'])){
                $this->username = $_SESSION['username'];
                return true;
            }
            
            return false;
        }

        public function getUser(string $username): bool {
            // Haal user gegevens op uit database
            $query = "SELECT * FROM gebruiker WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Vul eigenschappen met waarden uit database
                $this->username = $row['username'];
                $this->email = $row['email'] ?? '';
                
                return true;
            }
            
            return false;
        }

        public function logout(){
            session_start();
            
            // Verwijder alle session variabelen
            session_unset();
            
            // Destroy de session
            session_destroy();
        }
    }
?>
