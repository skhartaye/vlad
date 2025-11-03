/**
 * Map Manager
 * Handles map operations and heat map visualization
 */
class MapManager {
    constructor() {
        this.apiBase = 'api/map-data.php';
        this.map = null;
        this.markers = [];
        this.markerLayer = null;
    }

    /**
     * Initialize Leaflet map
     */
    initMap() {
        if (!this.map) {
            this.map = L.map("map").setView([14.6091, 121.0223], 11);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
            
            // Add geocoder control
            L.Control.geocoder().addTo(this.map);
            
            // Initialize marker layer group
            this.markerLayer = L.layerGroup().addTo(this.map);
        }
    }

    /**
     * Load heat map data from backend
     * @param {Object} filters - Optional filters (disease_type, days)
     * @returns {Promise<Array>} Array of map data points
     */
    async loadHeatMapData(filters = {}) {
        try {
            let url = this.apiBase;
            const params = new URLSearchParams();
            
            if (filters.disease_type) {
                params.append('disease_type', filters.disease_type);
            }
            if (filters.days) {
                params.append('days', filters.days);
            }
            
            if (params.toString()) {
                url += '?' + params.toString();
            }
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to load map data');
            }

            return data.data || [];

        } catch (error) {
            console.error('Load heat map data error:', error);
            return [];
        }
    }

    /**
     * Render heat map with data points
     * @param {Array} data - Array of data points with lat, lng, disease_type
     */
    renderHeatMap(data) {
        // Clear existing markers
        this.clearMarkers();
        
        if (!data || data.length === 0) {
            console.log('No data to display on map');
            return;
        }
        
        // Add markers for each data point
        data.forEach(point => {
            this.addMarker(point.lat, point.lng, point);
        });
        
        // Fit map bounds to show all markers
        if (this.markers.length > 0) {
            const group = L.featureGroup(this.markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    /**
     * Add marker to map
     * @param {number} lat - Latitude
     * @param {number} lng - Longitude
     * @param {Object} data - Additional marker data
     */
    addMarker(lat, lng, data) {
        // Create custom icon based on disease type
        const iconColor = data.color_code || '#FF0000';
        
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: ${iconColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.5);"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        
        const marker = L.marker([lat, lng], { icon: customIcon });
        
        // Add popup with disease information
        const popupContent = `
            <div style="font-family: Arial, sans-serif;">
                <strong style="color: ${iconColor};">${this.capitalize(data.disease_type)}</strong><br>
                <small>${new Date(data.date).toLocaleDateString()}</small>
            </div>
        `;
        marker.bindPopup(popupContent);
        
        // Add to marker layer
        marker.addTo(this.markerLayer);
        this.markers.push(marker);
    }

    /**
     * Filter heat map by disease type
     * @param {string} diseaseType - Disease type to filter
     */
    async filterByDisease(diseaseType) {
        const data = await this.loadHeatMapData({ disease_type: diseaseType });
        this.renderHeatMap(data);
    }

    /**
     * Clear all markers from map
     */
    clearMarkers() {
        if (this.markerLayer) {
            this.markerLayer.clearLayers();
        }
        this.markers = [];
    }

    /**
     * Geocode address to coordinates
     * @param {string} address - Address to geocode
     * @returns {Promise<Object>} Coordinates object {lat, lng}
     */
    async geocodeAddress(address) {
        return new Promise((resolve, reject) => {
            const geocoder = L.Control.Geocoder.nominatim();
            
            geocoder.geocode(address, (results) => {
                if (results && results.length > 0) {
                    const result = results[0];
                    resolve({
                        lat: result.center.lat,
                        lng: result.center.lng
                    });
                } else {
                    reject(new Error('Address not found'));
                }
            });
        });
    }

    /**
     * Capitalize first letter of string
     * @param {string} str - String to capitalize
     * @returns {string} Capitalized string
     */
    capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Get map instance
     * @returns {Object} Leaflet map instance
     */
    getMap() {
        return this.map;
    }
}

// Create global instance
const mapManager = new MapManager();
