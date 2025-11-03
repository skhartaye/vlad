/**
 * Report Manager
 * Handles case report operations with backend API
 */
class ReportManager {
    constructor() {
        this.apiBase = 'api/cases.php';
    }

    /**
     * Submit new case report
     * @param {Object} reportData - Report data (disease_type, address)
     * @returns {Promise<Object>} Response object
     */
    async submitReport(reportData) {
        try {
            const response = await fetch(`${this.apiBase}?action=create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(reportData)
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to submit report');
            }

            return data;

        } catch (error) {
            console.error('Submit report error:', error);
            throw error;
        }
    }

    /**
     * Get user's case reports
     * @param {number} userId - User ID
     * @returns {Promise<Array>} Array of user's reports
     */
    async getUserReports(userId) {
        try {
            const response = await fetch(`${this.apiBase}?action=list&user_id=${userId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to fetch reports');
            }

            return data.reports || [];

        } catch (error) {
            console.error('Get user reports error:', error);
            return [];
        }
    }

    /**
     * Update case report
     * @param {number} caseId - Case report ID
     * @param {Object} updateData - Updated data
     * @returns {Promise<Object>} Response object
     */
    async updateReport(caseId, updateData) {
        try {
            const response = await fetch(`${this.apiBase}?action=update`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    case_id: caseId,
                    ...updateData
                })
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to update report');
            }

            return data;

        } catch (error) {
            console.error('Update report error:', error);
            throw error;
        }
    }

    /**
     * Delete case report
     * @param {number} caseId - Case report ID
     * @returns {Promise<Object>} Response object
     */
    async deleteReport(caseId) {
        try {
            const response = await fetch(`${this.apiBase}?action=delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    case_id: caseId
                })
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to delete report');
            }

            return data;

        } catch (error) {
            console.error('Delete report error:', error);
            throw error;
        }
    }

    /**
     * Render report table with data
     * @param {Array} reports - Array of report objects
     */
    renderReportTable(reports) {
        const tbody = document.querySelector("#reportTable tbody");
        tbody.innerHTML = "";
        
        if (!reports || reports.length === 0) {
            const tr = document.createElement("tr");
            tr.innerHTML = `<td colspan="3" style="color:#6b6b6b;">No reports yet</td>`;
            tbody.appendChild(tr);
            return;
        }
        
        reports.forEach(report => {
            const tr = document.createElement("tr");
            const date = new Date(report.created_at).toLocaleString();
            tr.innerHTML = `
                <td>${this.escapeHtml(report.disease_type)}</td>
                <td>${this.escapeHtml(report.address)}</td>
                <td>${date}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    escapeHtml(text) {
        if (!text && text !== 0) return "";
        return text.toString().replace(/[&<>"']/g, function(m) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m];
        });
    }
}

// Create global instance
const reportManager = new ReportManager();
