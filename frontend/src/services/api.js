import axios from 'axios';

// Replace this with your actual Clever Cloud URL
const RENDER_BACKEND_URL = import.meta.env.VITE_API_URL || 'https://app-2fae98fb-338a-4372-b455-37ea171ae30a.cleverapps.io';

const getApiBaseURL = () => {
  // Allow dynamic runtime override via localStorage (very useful for testing/changing endpoints)
  const localStorageUrl = localStorage.getItem('BACKEND_URL');
  if (localStorageUrl) {
    return localStorageUrl;
  }

  // If running on GitHub Pages, route API requests to Render backend
  if (window.location.hostname.endsWith('github.io')) {
    if (import.meta.env.VITE_API_URL) {
      return import.meta.env.VITE_API_URL.endsWith('/api') 
        ? import.meta.env.VITE_API_URL 
        : `${import.meta.env.VITE_API_URL}/backend/api`;
    }
    return RENDER_BACKEND_URL.endsWith('/api')
      ? RENDER_BACKEND_URL
      : `${RENDER_BACKEND_URL}/backend/api`;
  }

  if (import.meta.env.DEV) {
    return '/api';
  }
  // Detect base path dynamically (e.g. /rct-education-web from /rct-education-web/frontend/index.php)
  const path = window.location.pathname;
  const frontendIndex = path.indexOf('/frontend');
  if (frontendIndex !== -1) {
    const basePath = path.substring(0, frontendIndex);
    return `${basePath}/backend/api`;
  }
  return '/rct-education-web/backend/api';
};

const api = axios.create({
  baseURL: getApiBaseURL(),
  withCredentials: true, // Required for PHP sessions to persist across requests
  headers: {
    'Content-Type': 'application/json'
  }
});

export default api;
