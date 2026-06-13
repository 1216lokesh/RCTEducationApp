import React, { useState } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate } from 'react-router-dom';
import { FileSignature, AlertCircle } from 'lucide-react';
import api from '../../services/api';

export default function Consent() {
  const { user } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();

  const [agree, setAgree] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (!agree) {
      setError('Please check the consent box first');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/admin/save_consent.php', {
        user_id: user.id,
        consent_given: 'yes'
      });

      if (response.data.status === 'success') {
        navigate('/patient/satisfaction');
      } else {
        setError(response.data.message || 'Error saving consent');
        setLoading(false);
      }
    } catch (err) {
      setError('Server error, please try again.');
      setLoading(false);
    }
  };

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{t('consent_title')}</h3>
        <p className="survey-subtitle">{t('consent_understand')}</p>

        <div className="consent-text-box">
          {t('consent_text')
            ? t('consent_text').split('\n\n').map((line, idx) => (
                <p key={idx} className="mb-2">{line}</p>
              ))
            : null}
        </div>

        {error && (
          <div className="auth-error">
            <AlertCircle size={20} />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <label className={`option-item flex-start p-3 ${agree ? 'checked' : ''} mb-4`}>
            <input
              type="checkbox"
              checked={agree}
              onChange={(e) => setAgree(e.target.checked)}
              className="mt-1"
              required
            />
            <span style={{ fontSize: '0.9rem', lineHeight: '1.4' }}>{t('consent_checkbox')}</span>
          </label>

          <button type="submit" className="survey-submit-btn" disabled={loading}>
            <span>{loading ? t('loading') : (t('i_agree') || 'I Agree - Proceed')}</span>
            <FileSignature size={18} />
          </button>
        </form>
      </div>
    </div>
  );
}
