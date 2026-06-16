import { createContext, useContext, useState, useEffect } from 'react';
import api from '../services/api';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const checkSession = async () => {
    try {
      const response = await api.get('/auth/me.php');
      if (response.data.success && response.data.loggedIn) {
        setUser(response.data.user);
      } else {
        setUser(null);
      }
    } catch {
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  const login = async (email, password, rememberMe) => {
    try {
      const response = await api.post('/auth/login.php', { 
        email, 
        password, 
        remember_me: rememberMe 
      });
      if (response.data.success) {
        setUser(response.data.user);
        return { success: true };
      }
      return { success: false, message: response.data.message || 'Login failed' };
    } catch (err) {
      return {
        success: false,
        message: err.response?.data?.message || 'Invalid credentials or server error'
      };
    }
  };

  const logout = async () => {
    try {
      await api.post('/auth/logout.php');
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      setUser(null);
    }
  };

  useEffect(() => {
    checkSession();
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, checkSession, setUser }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
