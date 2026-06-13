import axios from 'axios';

const getApiBaseURL = () => {
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
