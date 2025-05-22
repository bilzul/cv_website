<?php
class Database
{
    // XAMPP Database Configuration
    private $host = "localhost";
    private $db_name = "cv_db";
    private $username = "root";
    private $password = "";
    private $conn;

    // Connect to database
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            // Log error rather than displaying for security in production
            error_log("Database Connection Error: " . $e->getMessage());
            // Display user-friendly message
            echo '<div class="alert alert-danger mt-4">
                <p><strong>Database connection error.</strong> Please check your configuration or contact the administrator.</p>
                <p>Error details (for development only): ' . $e->getMessage() . '</p>
            </div>';
        }

        return $this->conn;
    }
}
