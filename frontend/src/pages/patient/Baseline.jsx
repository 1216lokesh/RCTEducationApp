import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowRight, AlertCircle } from 'lucide-react';
import api from '../../services/api';

export default function Baseline() {
  const { user } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();
  const { aptId } = useParams();
  const apt = parseInt(aptId) || 1;

  const [q1, setQ1] = useState('');
  const [q2, setQ2] = useState('');
  const [q3, setQ3] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // Log attendance automatically on load
    const logAttendance = async () => {
      try {
        await api.post('/admin/save_attendance.php', {
          user_id: user.id,
          apt: `apt${apt}`
        });
      } catch (err) {
        console.error('Error logging attendance:', err);
      }
    };
    logAttendance();
  }, [user.id, apt]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (!q1 || !q2 || !q3) {
      setError(t('please_answer_all') || 'Please answer all questions');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/patient/save_baseline.php', {
        patient_id: user.id,
        appointment: `apt${apt}`,
        q1,
        q2,
        q3
      });

      if (response.data.status === 'success') {
        if (apt === 1) {
          navigate('/patient/procedure-info/1');
        } else {
          navigate(`/patient/education/${apt}`);
        }
      } else {
        setError(response.data.message || 'Error saving responses');
        setLoading(false);
      }
    } catch (err) {
      setError('Server error, please try again.');
      setLoading(false);
    }
  };

  const titleKey = `apt${apt}_baseline_title`;
  const q1Key = `apt${apt}_q1`;
  const q2Key = `apt${apt}_q2`;
  const q3Key = `apt${apt}_q3`;
  const optPrefix = `apt${apt}_`;

  const getOptions = (questionNum) => {
    const qKey = `q${questionNum}`;
    return [
      t(`${optPrefix}${qKey}_a`),
      t(`${optPrefix}${qKey}_b`),
      t(`${optPrefix}${qKey}_c`)
    ];
  };

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{t(titleKey)}</h3>
        <p className="survey-subtitle">{t('apt1_subtitle')}</p>

        {error && (
          <div className="auth-error">
            <AlertCircle size={20} />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          {/* Question 1 */}
          <div className="survey-question">
            <label className="question-label">{t(q1Key)}</label>
            <div className="options-list">
              {getOptions(1).map((optText, idx) => (
                <label key={idx} className={`option-item ${q1 === optText ? 'checked' : ''}`}>
                  <input
                    type="radio"
                    name="q1"
                    value={optText}
                    checked={q1 === optText}
                    onChange={(e) => setQ1(e.target.value)}
                    required
                  />
                  <span>{optText}</span>
                </label>
              ))}
            </div>
          </div>

          {/* Question 2 */}
          <div className="survey-question">
            <label className="question-label">{t(q2Key)}</label>
            <div className="options-list">
              {getOptions(2).map((optText, idx) => (
                <label key={idx} className={`option-item ${q2 === optText ? 'checked' : ''}`}>
                  <input
                    type="radio"
                    name="q2"
                    value={optText}
                    checked={q2 === optText}
                    onChange={(e) => setQ2(e.target.value)}
                    required
                  />
                  <span>{optText}</span>
                </label>
              ))}
            </div>
          </div>

          {/* Question 3 */}
          <div className="survey-question">
            <label className="question-label">{t(q3Key)}</label>
            <div className="options-list">
              {getOptions(3).map((optText, idx) => (
                <label key={idx} className={`option-item ${q3 === optText ? 'checked' : ''}`}>
                  <input
                    type="radio"
                    name="q3"
                    value={optText}
                    checked={q3 === optText}
                    onChange={(e) => setQ3(e.target.value)}
                    required
                  />
                  <span>{optText}</span>
                </label>
              ))}
            </div>
          </div>

          <button type="submit" className="survey-submit-btn" disabled={loading}>
            <span>{loading ? t('loading') : t('next')}</span>
            <ArrowRight size={18} />
          </button>
        </form>
      </div>
    </div>
  );
}
