<?php
/**
 * Case Report Model Class
 * Handles case report CRUD operations
 */
class CaseReport {
    private $conn;
    private $table_name = "case_reports";
    private $disease_table = "disease_types";

    // Case report properties
    public $id;
    public $user_id;
    public $disease_type;
    public $disease_type_id;
    public $address;
    public $latitude;
    public $longitude;
    public $created_at;
    public $updated_at;

    /**
     * Constructor
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new case report
     * @return bool|int Case ID on success, false on failure
     */
    public function create() {
        try {
            // Get disease_type_id from disease name
            $disease_id = $this->getDiseaseTypeId($this->disease_type);
            
            if (!$disease_id) {
                error_log("Invalid disease type: " . $this->disease_type);
                return false;
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     (user_id, disease_type_id, address, latitude, longitude) 
                     VALUES (:user_id, :disease_type_id, :address, :latitude, :longitude)";

            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->address = htmlspecialchars(strip_tags($this->address));

            // Bind parameters
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':disease_type_id', $disease_id);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':latitude', $this->latitude);
            $stmt->bindParam(':longitude', $this->longitude);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return $this->id;
            }

            return false;

        } catch (PDOException $e) {
            error_log("Case Report Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all case reports for heat map
     * @return array Array of case reports
     */
    public function read() {
        try {
            $query = "SELECT cr.id, cr.user_id, cr.address, cr.latitude, cr.longitude, 
                            cr.created_at, dt.name as disease_type, dt.color_code
                     FROM " . $this->table_name . " cr
                     INNER JOIN " . $this->disease_table . " dt ON cr.disease_type_id = dt.id
                     ORDER BY cr.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Case Report Read Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get case reports by user ID
     * @return array Array of user's case reports
     */
    public function readByUser() {
        try {
            $query = "SELECT cr.id, cr.address, cr.latitude, cr.longitude, 
                            cr.created_at, cr.updated_at, dt.name as disease_type, dt.color_code
                     FROM " . $this->table_name . " cr
                     INNER JOIN " . $this->disease_table . " dt ON cr.disease_type_id = dt.id
                     WHERE cr.user_id = :user_id
                     ORDER BY cr.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Case Report Read By User Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update case report
     * @return bool True on success, false on failure
     */
    public function update() {
        try {
            // First verify ownership
            if (!$this->verifyOwnership()) {
                error_log("Ownership verification failed for case report ID: " . $this->id);
                return false;
            }

            // Get disease_type_id from disease name
            $disease_id = $this->getDiseaseTypeId($this->disease_type);
            
            if (!$disease_id) {
                return false;
            }

            $query = "UPDATE " . $this->table_name . " 
                     SET disease_type_id = :disease_type_id, 
                         address = :address, 
                         latitude = :latitude, 
                         longitude = :longitude
                     WHERE id = :id AND user_id = :user_id";

            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $this->address = htmlspecialchars(strip_tags($this->address));

            // Bind parameters
            $stmt->bindParam(':disease_type_id', $disease_id);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':latitude', $this->latitude);
            $stmt->bindParam(':longitude', $this->longitude);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':user_id', $this->user_id);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Case Report Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete case report
     * @return bool True on success, false on failure
     */
    public function delete() {
        try {
            // First verify ownership
            if (!$this->verifyOwnership()) {
                error_log("Ownership verification failed for case report ID: " . $this->id);
                return false;
            }

            $query = "DELETE FROM " . $this->table_name . " 
                     WHERE id = :id AND user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':user_id', $this->user_id);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Case Report Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent case reports within specified days
     * @param int $days Number of days to look back
     * @return array Array of recent case reports
     */
    public function readRecent($days = 90) {
        try {
            $query = "SELECT cr.id, cr.user_id, cr.address, cr.latitude, cr.longitude, 
                            cr.created_at, dt.name as disease_type, dt.color_code
                     FROM " . $this->table_name . " cr
                     INNER JOIN " . $this->disease_table . " dt ON cr.disease_type_id = dt.id
                     WHERE cr.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                     ORDER BY cr.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Case Report Read Recent Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get case reports filtered by disease type
     * @param string $type Disease type name
     * @return array Array of filtered case reports
     */
    public function readByDisease($type) {
        try {
            $query = "SELECT cr.id, cr.user_id, cr.address, cr.latitude, cr.longitude, 
                            cr.created_at, dt.name as disease_type, dt.color_code
                     FROM " . $this->table_name . " cr
                     INNER JOIN " . $this->disease_table . " dt ON cr.disease_type_id = dt.id
                     WHERE dt.name = :disease_type
                     ORDER BY cr.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':disease_type', $type);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Case Report Read By Disease Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verify that the user owns this case report
     * @return bool True if user owns report, false otherwise
     */
    private function verifyOwnership() {
        try {
            $query = "SELECT user_id FROM " . $this->table_name . " 
                     WHERE id = :id LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                return $row['user_id'] == $this->user_id;
            }

            return false;

        } catch (PDOException $e) {
            error_log("Ownership Verification Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get disease type ID from disease name
     * @param string $name Disease type name
     * @return int|bool Disease type ID or false
     */
    private function getDiseaseTypeId($name) {
        try {
            $query = "SELECT id FROM " . $this->disease_table . " 
                     WHERE name = :name LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                return $row['id'];
            }

            return false;

        } catch (PDOException $e) {
            error_log("Get Disease Type ID Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
