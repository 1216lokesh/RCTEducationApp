import axios from 'axios';

// Replace this with your actual Render URL after deploying it
const RENDER_BACKEND_URL = 'https://rct-education-web.onrender.com';

const getApiBaseURL = () => {
  // If running on GitHub Pages, route API requests to Render backend
  if (window.location.hostname.endsWith('github.io')) {
    return `${RENDER_BACKEND_URL}/backend/api`;
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
