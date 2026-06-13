import React, { useState } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate, Link } from 'react-router-dom';
import { UserPlus, AlertCircle } from 'lucide-react';
import api from '../../services/api';

export default function Register() {
  const { login } = useAuth();
  const { t, language, setLanguage } = useLanguage();
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    date_of_birth: '',
    gender: '',
    language: language,
    password: '',
    confirm_password: ''
  });

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrors({});
    setLoading(true);

    if (formData.password !== formData.confirm_password) {
      setErrors({ confirm_password: t('password_mismatch') });
      setLoading(false);
      return;
    }

    try {
      const response = await api.post('/auth/register.php', formData);
      if (response.data.success) {
        // Log in user on success
        const loginRes = await login(formData.email, formData.password, false);
        if (loginRes.success) {
          navigate('/');
        } else {
          navigate('/login');
        }
      } else {
        setErrors({ general: response.data.message || t('registration_failed') });
        setLoading(false);
      }
    } catch (err) {
      setErrors({
        general: err.response?.data?.message || t('registration_failed')
      });
      setLoading(false);
    }
  };

  return (
    <div className="auth-container">
      <div className="auth-card wide-card">
        <div className="auth-header">
          <div className="auth-logo">
            <UserPlus size={32} />
          </div>
          <h2>{t('register')}</h2>
          <p>{t('register_subtitle')}</p>
        </div>

        {errors.general && (
          <div className="auth-error">
            <AlertCircle size={20} />
            <span>{errors.general}</span>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div className="form-row">
            <div className="form-group">
              <label htmlFor="first_name">{t('first_name')}</label>
              <input
                type="text"
                id="first_name"
                name="first_name"
                value={formData.first_name}
                onChange={handleChange}
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="last_name">{t('last_name')}</label>
              <input
                type="text"
                id="last_name"
                name="last_name"
                value={formData.last_name}
                onChange={handleChange}
                required
              />
            </div>
          </div>

          <div className="form-group">
            <label htmlFor="email">{t('email')}</label>
            <input
              type="email"
              id="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
            />
          </div>

          <div className="form-group">
            <label htmlFor="phone">{t('phone')}</label>
            <input
              type="tel"
              id="phone"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
            />
          </div>

          <div className="form-row">
            <div className="form-group">
              <label htmlFor="date_of_birth">{t('date_of_birth')}</label>
              <input
                type="text"
                id="date_of_birth"
                name="date_of_birth"
                placeholder="YYYY-MM-DD"
                value={formData.date_of_birth}
                onChange={handleChange}
              />
            </div>

            <div className="form-group">
              <label htmlFor="gender">{t('gender')}</label>
              <select
                id="gender"
                name="gender"
                value={formData.gender}
                onChange={handleChange}
              >
                <option value="">{t('select')} {t('gender')}</option>
                <option value="M">{t('male')}</option>
                <option value="F">{t('female')}</option>
                <option value="Other">{t('other')}</option>
              </select>
            </div>
          </div>

          <div className="form-group">
            <label htmlFor="language">{t('language')}</label>
            <select
              id="language"
              name="language"
              value={formData.language}
              onChange={(e) => {
                handleChange(e);
                setLanguage(e.target.value);
              }}
            >
              <option value="en">English</option>
              <option value="ta">Tamil (தமிழ்)</option>
              <option value="hi">Hindi (हिंदी)</option>
              <option value="te">Telugu (తెలుగు)</option>
            </select>
          </div>

          <div className="form-group">
            <label htmlFor="password">{t('password')}</label>
            <input
              type="password"
              id="password"
              name="password"
              value={formData.password}
              onChange={handleChange}
              required
            />
          </div>

          <div className="form-group">
            <label htmlFor="confirm_password">{t('confirm_password')}</label>
            <input
              type="password"
              id="confirm_password"
              name="confirm_password"
              value={formData.confirm_password}
              onChange={handleChange}
              required
            />
            {errors.confirm_password && <span className="error-text">{errors.confirm_password}</span>}
          </div>

          <button type="submit" className="auth-submit-btn" disabled={loading}>
            {loading ? t('loading') : t('register')}
          </button>
        </form>

        <div className="auth-footer">
          <p>
            {t('already_have_account') || "Already have an account?"}{' '}
            <Link to="/login">{t('login')}</Link>
          </p>
        </div>

        <div className="lang-selector-bottom">
          {['en', 'ta', 'hi', 'te'].map((lang) => (
            <button
              key={lang}
              type="button"
              className={`lang-btn ${language === lang ? 'active' : ''}`}
              onClick={() => {
                setLanguage(lang);
                setFormData({ ...formData, language: lang });
              }}
            >
              {lang.toUpperCase()}
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}
