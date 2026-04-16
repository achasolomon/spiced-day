/**
 * Global AJAX Form Handler
 * Handles form submissions via AJAX with proper error handling
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle forms with data-ajax attribute
    document.querySelectorAll('form[data-ajax="true"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML || submitButton.value : '';
            const method = form.method || 'POST';
            const action = form.action || window.location.href;
            
            // Disable submit button
            if (submitButton) {
                submitButton.disabled = true;
                if (submitButton.tagName === 'BUTTON') {
                    submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
                } else {
                    submitButton.value = 'Processing...';
                }
            }
            
            // Add _method if needed
            if (method.toUpperCase() !== 'GET' && method.toUpperCase() !== 'POST') {
                formData.append('_method', method.toUpperCase());
            }
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.content);
            }
            
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => ({ response, data }));
                }
                return response.text().then(text => ({ response, data: null, text }));
            })
            .then(({ response, data, text }) => {
                // Re-enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                    if (submitButton.tagName === 'BUTTON') {
                        submitButton.innerHTML = originalText;
                    } else {
                        submitButton.value = originalText;
                    }
                }
                
                if (response.ok) {
                    // Success
                    if (data) {
                        if (data.success && data.message) {
                            if (typeof showToast === 'function') {
                                showToast(data.message, 'success');
                            }
                        }
                        
                        // Handle redirect
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        } else if (data.reload) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        // HTML response - might be a redirect
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else if (typeof showToast === 'function') {
                            showToast('Operation completed successfully', 'success');
                        }
                    }
                } else {
                    // Error handling
                    if (data) {
                        if (data.errors) {
                            // Validation errors
                            Object.keys(data.errors).forEach(field => {
                                const fieldElement = form.querySelector(`[name="${field}"]`);
                                if (fieldElement) {
                                    fieldElement.classList.add('border-red-500');
                                    const errorMsg = Array.isArray(data.errors[field]) 
                                        ? data.errors[field][0] 
                                        : data.errors[field];
                                    if (typeof showToast === 'function') {
                                        showToast(errorMsg, 'error');
                                    }
                                }
                            });
                        } else if (data.message) {
                            if (typeof showToast === 'function') {
                                showToast(data.message, 'error');
                            }
                        }
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('An error occurred. Please try again.', 'error');
                        }
                    }
                }
            })
            .catch(error => {
                // Re-enable submit button
                if (submitButton) {
                    submitButton.disabled = false;
                    if (submitButton.tagName === 'BUTTON') {
                        submitButton.innerHTML = originalText;
                    } else {
                        submitButton.value = originalText;
                    }
                }
                
                console.error('AJAX Error:', error);
                if (typeof showToast === 'function') {
                    showToast('Network error. Please check your connection and try again.', 'error');
                }
            });
        });
    });
    
    // Handle AJAX links
    document.querySelectorAll('a[data-ajax="true"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = link.href;
            const method = link.dataset.method || 'GET';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.message) {
                    if (typeof showToast === 'function') {
                        showToast(data.message, 'success');
                    }
                }
                
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.reload) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                if (typeof showToast === 'function') {
                    showToast('An error occurred. Please try again.', 'error');
                }
            });
        });
    });
});

