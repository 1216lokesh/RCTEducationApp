import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate } from 'react-router-dom';
import { ArrowRight, AlertCircle } from 'lucide-react';
import api from '../../services/api';

export default function Satisfaction() {
  const { user } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();

  const [procedureId, setProcedureId] = useState(1);
  const [answers, setAnswers] = useState({
    q1: '', q2: '', q3: '', q4: '', q5: ''
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchProcedure = async () => {
      try {
        const response = await api.post('/patient/get_my_procedure.php', { patient_id: user.id });
        if (response.data.status === 'success') {
          setProcedureId(response.data.data.procedure_id);
        }
      } catch (err) {
        console.error('Error fetching procedure:', err);
      }
    };
    fetchProcedure();
  }, [user.id]);

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
      setError(t('please_answer') || 'Please answer all questions');
      return;
    }

    setLoading(true);

    const totalScore = values.reduce((sum, val) => sum + parseInt(val), 0);

    try {
      const response = await api.post('/patient/save_satisfaction.php', {
        patient_id: user.id,
        procedure_id: procedureId,
        timepoint: 'post_counselling',
        score: totalScore
      });

      if (response.data.status === 'success') {
        navigate('/');
      } else {
        setError(response.data.message || 'Error saving satisfaction rating');
        setLoading(false);
      }
    } catch (err) {
      setError('Server error, please try again.');
      setLoading(false);
    }
  };

  const questions = [
    t('satisfaction_q1'),
    t('satisfaction_q2'),
    t('satisfaction_q3'),
    t('satisfaction_q4'),
    t('satisfaction_q5')
  ];

  const opts = [
    { label: t('satisfaction_opt1'), value: 1 },
    { label: t('satisfaction_opt2'), value: 2 },
    { label: t('satisfaction_opt3'), value: 3 },
    { label: t('satisfaction_opt4'), value: 4 },
    { label: t('satisfaction_opt5'), value: 5 }
  ];

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{t('satisfaction_title')}</h3>
        <p className="survey-subtitle">{t('satisfaction_subtitle')}</p>

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
                  {opts.map((opt, idx) => (
                    <label
                      key={idx}
                      className={`option-item ${answers[qKey] === opt.value ? 'checked' : ''}`}
                    >
                      <input
                        type="radio"
                        name={qKey}
                        value={opt.value}
                        checked={answers[qKey] === opt.value}
                        onChange={() => handleSelect(qKey, opt.value)}
                        required
                      />
                      <span>{opt.label}</span>
                    </label>
                  ))}
                </div>
              </div>
            );
          })}

          <button type="submit" className="survey-submit-btn" disabled={loading}>
            <span>{loading ? t('loading') : t('next')}</span>
            <ArrowRight size={18} />
          </button>
        </form>
      </div>
    </div>
  );
}
