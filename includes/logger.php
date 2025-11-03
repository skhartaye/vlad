<?php
/**
 * Error Logging System
 * Centralized logging for application errors and events
 */

class Logger {
    private $logDir;
    private $logFile;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->logDir = __DIR__ . '/../logs';
        
        // Create logs directory if it doesn't exist
        if (!file_exists($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
        
        $this->logFile = $this->logDir . '/app.log';
    }
    
    /**
     * Log error message
     * @param string $message Error message
     * @param array $context Additional context
     */
    public function error($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }
    
    /**
     * Log warning message
     * @param string $message Warning message
     * @param array $context Additional context
     */
    public function warning($message, $context = []) {
        $this->log('WARNING', $message, $context);
    }
    
    /**
     * Log info message
     * @param string $message Info message
     * @param array $context Additional context
     */
    public function info($message, $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    /**
     * Log debug message
     * @param string $message Debug message
     * @param array $context Additional context
     */
    public function debug($message, $context = []) {
        if (ENVIRONMENT === 'development') {
            $this->log('DEBUG', $message, $context);
        }
    }
    
    /**
     * Log message with level
     * @param string $level Log level
     * @param string $message Message
     * @param array $context Additional context
     */
    private function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
        
        $logEntry = sprintf(
            "[%s] [%s] [IP: %s] [User: %s] %s",
            $timestamp,
            $level,
            $ip,
            $user,
            $message
        );
        
        if (!empty($context)) {
            $logEntry .= ' | Context: ' . json_encode($context);
        }
        
        $logEntry .= PHP_EOL;
        
        // Write to log file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        // Also log to PHP error log in production
        if (ENVIRONMENT === 'production') {
            error_log($logEntry);
        }
    }
    
    /**
     * Log database error
     * @param PDOException $e Exception
     * @param string $query SQL query (optional)
     */
    public function logDatabaseError($e, $query = '') {
        $context = [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        
        if (!empty($query)) {
            $context['query'] = $query;
        }
        
        $this->error('Database Error', $context);
    }
    
    /**
     * Log authentication event
     * @param string $event Event type (login, logout, register, etc.)
     * @param string $username Username
     * @param bool $success Success status
     */
    public function logAuth($event, $username, $success) {
        $message = sprintf(
            "Authentication %s: %s - %s",
            $event,
            $username,
            $success ? 'SUCCESS' : 'FAILED'
        );
        
        $this->info($message);
    }
    
    /**
     * Log API request
     * @param string $endpoint Endpoint
     * @param string $method HTTP method
     * @param int $statusCode Response status code
     */
    public function logApiRequest($endpoint, $method, $statusCode) {
        $message = sprintf(
            "API Request: %s %s - Status: %d",
            $method,
            $endpoint,
            $statusCode
        );
        
        $this->info($message);
    }
    
    /**
     * Get recent log entries
     * @param int $lines Number of lines to retrieve
     * @return array Log entries
     */
    public function getRecentLogs($lines = 100) {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $file = new SplFileObject($this->logFile, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        
        $startLine = max(0, $lastLine - $lines);
        $logs = [];
        
        $file->seek($startLine);
        while (!$file->eof()) {
            $line = $file->current();
            if (!empty(trim($line))) {
                $logs[] = $line;
            }
            $file->next();
        }
        
        return $logs;
    }
    
    /**
     * Clear log file
     */
    public function clearLogs() {
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }
}

// Create global logger instance
$logger = new Logger();
?>
