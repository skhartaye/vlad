<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>

<style>
    nav {
        background-color: #198754;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    nav .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    nav ul {
        list-style: none;
        display: flex;
        gap: 2rem;
        padding: 1rem 0;
    }
    
    nav a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.2s ease;
    }
    
    nav a:hover {
        opacity: 0.8;
    }
    
    .nav-user {
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .nav-user span {
        font-weight: 600;
    }
    
    .btn-logout {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }
    
    .btn-logout:hover {
        background-color: #c82333;
    }
</style>

<nav>
    <div class="container">
        <ul>
            <li><a href="index.html">Home</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="index.html#map">Heat Map</a></li>
                <li><a href="index.html#reports">My Reports</a></li>
            <?php else: ?>
                <li><a href="index.html#map">View Heat Map</a></li>
                <li><a href="index.html#login">Login</a></li>
                <li><a href="index.html#signup">Sign Up</a></li>
            <?php endif; ?>
        </ul>
        
        <?php if ($isLoggedIn): ?>
            <div class="nav-user">
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                <button class="btn-logout" onclick="handleLogout()">Logout</button>
            </div>
        <?php endif; ?>
    </div>
</nav>

<script>
async function handleLogout() {
    try {
        const response = await fetch('api/auth.php?action=logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            window.location.href = 'index.html';
        }
    } catch (error) {
        console.error('Logout error:', error);
        window.location.href = 'index.html';
    }
}
</script>
