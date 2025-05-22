<?php
class Database {
    // InfinityFree Database Configuration - Update with your actual details
    private $host = "sql107.infinityfree.com"; // InfinityFree MySQL host
    private $db_name = "if0_38920142_cv_db"; // Replace with your actual database name
    private $username = "if0_38920142"; // Replace with your actual database username
    private $password = "ZZqmhcGaXz"; // Replace with your actual database password
    private $conn;

    // Connect to database
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                                 $this->username, 
                                 $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            // Log error rather than displaying for security in production
            error_log("Database Connection Error: " . $e->getMessage());
            echo "Connection error. Please contact the administrator.";
        }

        return $this->conn;
    }
}
?> 