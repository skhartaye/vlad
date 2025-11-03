/**
 * Authentication Manager
 * Handles user authentication with backend API
 */
class AuthManager {
    constructor() {
        this.apiBase = 'api/auth.php';
        this.currentUser = null;
    }

    /**
     * Register new user
     * @param {Object} userData - User registration data
     * @returns {Promise<Object>} Response object
     */
    async register(userData) {
        try {
            const response = await fetch(`${this.apiBase}?action=register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(userData)
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Registration failed');
            }

            return data;

        } catch (error) {
            console.error('Registration error:', error);
            throw error;
        }
    }

    /**
     * Login user
     * @param {Object} credentials - Username and password
     * @returns {Promise<Object>} Response object with user data
     */
    async login(credentials) {
        try {
            const response = await fetch(`${this.apiBase}?action=login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(credentials)
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Login failed');
            }

            // Store user data
            this.currentUser = data.user;
            
            return data;

        } catch (error) {
            console.error('Login error:', error);
            throw error;
        }
    }

    /**
     * Logout user
     * @returns {Promise<Object>} Response object
     */
    async logout() {
        try {
            const response = await fetch(`${this.apiBase}?action=logout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            // Clear current user
            this.currentUser = null;
            
            return data;

        } catch (error) {
            console.error('Logout error:', error);
            throw error;
        }
    }

    /**
     * Check if user session is valid
     * @returns {Promise<Object>} Response object with authentication status
     */
    async checkSession() {
        try {
            const response = await fetch(`${this.apiBase}?action=check`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.authenticated && data.user) {
                this.currentUser = data.user;
            } else {
                this.currentUser = null;
            }
            
            return data;

        } catch (error) {
            console.error('Session check error:', error);
            this.currentUser = null;
            return { authenticated: false };
        }
    }

    /**
     * Check if user is authenticated
     * @returns {boolean} True if authenticated
     */
    isAuthenticated() {
        return this.currentUser !== null;
    }

    /**
     * Get current user data
     * @returns {Object|null} Current user object or null
     */
    getCurrentUser() {
        return this.currentUser;
    }
}

// Create global instance
const authManager = new AuthManager();
