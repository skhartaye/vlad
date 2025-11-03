<?php
/**
 * Log Viewer for Administrators
 * View application logs
 */

session_start();

// Simple authentication check (in production, use proper admin authentication)
$adminPassword = 'admin123'; // Change this!

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === $adminPassword) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = 'Invalid password';
        }
    }
    
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login - Log Viewer</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    background: #f5f5f5;
                    margin: 0;
                }
                .login-box {
                    background: white;
                    padding: 2rem;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    width: 300px;
                }
                h2 { margin-top: 0; color: #333; }
                input {
                    width: 100%;
                    padding: 0.5rem;
                    margin: 0.5rem 0;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    box-sizing: border-box;
                }
                button {
                    width: 100%;
                    padding: 0.7rem;
                    background: #198754;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-weight: bold;
                }
                button:hover { background: #157347; }
                .error { color: #dc3545; margin: 0.5rem 0; }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2>Admin Login</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="password" name="password" placeholder="Admin Password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

require_once '../includes/logger.php';

$action = $_GET['action'] ?? 'view';
$lines = isset($_GET['lines']) ? intval($_GET['lines']) : 100;

if ($action === 'clear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $logger->clearLogs();
    header('Location: view-logs.php');
    exit();
}

if ($action === 'logout') {
    unset($_SESSION['admin_logged_in']);
    header('Location: view-logs.php');
    exit();
}

$logs = $logger->getRecentLogs($lines);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Application Logs - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 1rem;
        }
        .header {
            background: #252526;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            color: #4ec9b0;
            font-size: 1.5rem;
        }
        .controls {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        select, button {
            padding: 0.5rem 1rem;
            border: 1px solid #3e3e42;
            background: #2d2d30;
            color: #d4d4d4;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #3e3e42;
        }
        .btn-danger {
            background: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .log-container {
            background: #252526;
            border-radius: 8px;
            padding: 1rem;
            max-height: 80vh;
            overflow-y: auto;
        }
        .log-entry {
            padding: 0.5rem;
            border-bottom: 1px solid #3e3e42;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        .log-entry.error {
            background: rgba(220, 53, 69, 0.1);
            border-left: 3px solid #dc3545;
        }
        .log-entry.warning {
            background: rgba(255, 193, 7, 0.1);
            border-left: 3px solid #ffc107;
        }
        .log-entry.info {
            background: rgba(13, 202, 240, 0.1);
            border-left: 3px solid #0dcaf0;
        }
        .log-entry.debug {
            background: rgba(108, 117, 125, 0.1);
            border-left: 3px solid #6c757d;
        }
        .timestamp {
            color: #4ec9b0;
        }
        .level {
            font-weight: bold;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        .level.error { background: #dc3545; color: white; }
        .level.warning { background: #ffc107; color: black; }
        .level.info { background: #0dcaf0; color: black; }
        .level.debug { background: #6c757d; color: white; }
        .empty {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Application Logs</h1>
        <div class="controls">
            <select onchange="window.location.href='?lines='+this.value">
                <option value="50" <?php echo $lines == 50 ? 'selected' : ''; ?>>Last 50 lines</option>
                <option value="100" <?php echo $lines == 100 ? 'selected' : ''; ?>>Last 100 lines</option>
                <option value="500" <?php echo $lines == 500 ? 'selected' : ''; ?>>Last 500 lines</option>
                <option value="1000" <?php echo $lines == 1000 ? 'selected' : ''; ?>>Last 1000 lines</option>
            </select>
            <button onclick="location.reload()">🔄 Refresh</button>
            <form method="POST" action="?action=clear" style="display: inline;" onsubmit="return confirm('Are you sure you want to clear all logs?')">
                <button type="submit" class="btn-danger">🗑️ Clear Logs</button>
            </form>
            <button onclick="window.location.href='?action=logout'">🚪 Logout</button>
        </div>
    </div>
    
    <div class="log-container">
        <?php if (empty($logs)): ?>
            <div class="empty">No log entries found</div>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <?php
                $class = '';
                if (strpos($log, '[ERROR]') !== false) $class = 'error';
                elseif (strpos($log, '[WARNING]') !== false) $class = 'warning';
                elseif (strpos($log, '[INFO]') !== false) $class = 'info';
                elseif (strpos($log, '[DEBUG]') !== false) $class = 'debug';
                
                // Parse log entry
                preg_match('/\[(.*?)\] \[(.*?)\]/', $log, $matches);
                $timestamp = $matches[1] ?? '';
                $level = $matches[2] ?? '';
                $message = preg_replace('/\[.*?\] \[.*?\]/', '', $log, 2);
                ?>
                <div class="log-entry <?php echo $class; ?>">
                    <span class="timestamp">[<?php echo htmlspecialchars($timestamp); ?>]</span>
                    <span class="level <?php echo strtolower($level); ?>"><?php echo htmlspecialchars($level); ?></span>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>
