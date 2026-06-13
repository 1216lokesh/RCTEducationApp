import React, { useState } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate } from 'react-router-dom';
import { Check, AlertCircle } from 'lucide-react';
import api from '../../services/api';

export default function Followup1Week() {
  const { user } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();

  const [answers, setAnswers] = useState({ q1: '', q2: '', q3: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSelect = (qKey, value) => {
    setAnswers({
      ...answers,
      [qKey]: value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    const values = Object.values(answers);
    if (values.includes('')) {
      setError(t('please_answer_all') || 'Please answer all questions');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/admin/save_score.php', {
        user_id: user.id,
        quiz: 'followup_1week',
        score: 1
      });

      if (response.data.status === 'success') {
        navigate('/');
      } else {
        setError(response.data.message || 'Error saving responses');
        setLoading(false);
      }
    } catch (err) {
      setError('Server error, please try again.');
      setLoading(false);
    }
  };

  const questions = [
    t('followup1week_q1'),
    t('followup1week_q2'),
    t('followup1week_q3')
  ];

  const getOptions = (questionNum) => {
    const qKey = `q${questionNum}`;
    return [
      t(`followup1week_${qKey}_a`),
      t(`followup1week_${qKey}_b`),
      t(`followup1week_${qKey}_c`)
    ];
  };

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{t('followup1week_title')}</h3>
        <p className="survey-subtitle">{t('followup1week_subtitle')}</p>

        {error && (
          <div className="auth-error">
            <AlertCircle size={20} />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          {questions.map((qText, qIdx) => {
            const qKey = `q${qIdx + 1}`;
            return (
              <div key={qIdx} className="survey-question">
                <label className="question-label">{qText}</label>
                <div className="options-list">
                  {getOptions(qIdx + 1).map((optText, idx) => (
                    <label
                      key={idx}
                      className={`option-item ${answers[qKey] === optText ? 'checked' : ''}`}
                    >
                      <input
                        type="radio"
                        name={qKey}
                        value={optText}
                        checked={answers[qKey] === optText}
                        onChange={() => handleSelect(qKey, optText)}
                        required
                      />
                      <span>{optText}</span>
                    </label>
                  ))}
                </div>
              </div>
            );
          })}

          <button type="submit" className="survey-submit-btn green-btn" disabled={loading}>
            <span>{loading ? t('loading') : t('submit')}</span>
            <Check size={18} />
          </button>
        </form>
      </div>
    </div>
  );
}
