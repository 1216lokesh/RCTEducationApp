/* RCT Education Portal - JavaScript */

// Document Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeComponents();
});

// Initialize all components
function initializeComponents() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('form[novalidate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    initializeApiForms();

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Show loading spinner
function showLoading(element) {
    const loader = document.createElement('div');
    loader.className = 'loading';
    element.appendChild(loader);
}

// Hide loading spinner
function hideLoading(element) {
    const loader = element.querySelector('.loading');
    if (loader) {
        loader.remove();
    }
}

// Display notification
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('main');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }
}

// Format date
function formatDate(date, format = 'Y-m-d H:i') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return format
        .replace('Y', year)
        .replace('m', month)
        .replace('d', day)
        .replace('H', hours)
        .replace('i', minutes);
}

// API Call helper
async function apiCall(endpoint, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin'
    };

    const config = { ...defaultOptions, ...options };

    try {
        const response = await fetch(endpoint, config);
        const payload = await response.json();
        if (!response.ok) {
            payload.status = response.status;
            throw payload;
        }
        return payload;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

function clearFormErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(node => node.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(node => node.textContent = '');
}

function setFormErrors(form, errors) {
    clearFormErrors(form);
    Object.keys(errors).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
            input.classList.add('is-invalid');
            let feedback = input.closest('.mb-3')?.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.closest('.mb-3')?.appendChild(feedback);
            }
            feedback.textContent = errors[key];
        }
    });
}

function handleApiFormSubmit(form, actionUrl, redirectCallback) {
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        clearFormErrors(form);
        showLoading(form.querySelector('button[type="submit"]'));

        try {
            const result = await apiCall(actionUrl, {
                method: 'POST',
                body: JSON.stringify(data)
            });

            if (result.success) {
                showNotification(result.message || 'Success', 'success');
                redirectCallback(result);
            } else {
                if (result.errors) {
                    setFormErrors(form, result.errors);
                }
                showNotification(result.message || 'Something went wrong.', 'danger');
            }
        } catch (error) {
            if (error.errors) {
                setFormErrors(form, error.errors);
            }
            const message = error.message || error.error || 'Server error. Please try again.';
            showNotification(message, 'danger');
        } finally {
            hideLoading(form.querySelector('button[type="submit"]'));
        }
    });
}

function initializeApiForms() {
    const apiForms = document.querySelectorAll('form[data-api-endpoint]');
    apiForms.forEach(form => {
        const endpoint = form.getAttribute('data-api-endpoint');
        if (!endpoint) return;

        const redirectPath = form.getAttribute('data-api-redirect-path');
        handleApiFormSubmit(form, endpoint, result => {
            if (redirectPath) {
                window.location.href = redirectPath;
                return;
            }

            if (result.user && result.user.role === 'patient') {
                window.location.href = '/patient/dashboard.php';
            } else if (result.user && result.user.role) {
                window.location.href = '/admin/dashboard.php';
            } else {
                window.location.reload();
            }
        });
    });
}

// Confirm dialog
function confirmAction(message) {
    return confirm(message);
}

// Table search
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent || row.innerText;
        row.style.display = text.toUpperCase().includes(filter) ? '' : 'none';
    }
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    let csv = [];
    
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            rowData.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(','));
    });

    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const link = document.createElement('a');
    link.setAttribute('href', encodeURI(csvContent));
    link.setAttribute('download', filename || 'export.csv');
    link.click();
}

// Format currency
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount);
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize tooltips on dynamic content
function reinitializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}
