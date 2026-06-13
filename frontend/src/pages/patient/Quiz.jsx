import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate, useParams } from 'react-router-dom';
import { Check, AlertCircle } from 'lucide-react';
import api from '../../services/api';

export default function Quiz() {
  const { user } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();
  const { aptId } = useParams();
  const apt = parseInt(aptId) || 1;

  const [procedureId, setProcedureId] = useState(1);
  const [answers, setAnswers] = useState({ q1: '', q2: '', q3: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [finalScore, setFinalScore] = useState(0);

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

    if (apt === 4) {
      navigate('/patient/followup-1week');
      return;
    }

    const q1 = parseInt(answers.q1);
    const q2 = parseInt(answers.q2);
    const q3 = parseInt(answers.q3);
    let score = 0;

    if (apt === 1) {
      if (q1 === 1) score++;
      if (q2 === 1) score++;
      if (q3 === 1) score++;
    } else if (apt === 2) {
      if (q1 === 1) score++;
      if (q2 === 0) score++;
      if (q3 === 1) score++;
    } else if (apt === 3) {
      if (q1 === 0) score++;
      if (q2 === 1) score++;
      if (q3 === 0) score++;
    }

    setFinalScore(score);

    try {
      await api.post('/admin/save_score.php', {
        user_id: user.id,
        quiz: `quiz${apt}`,
        score: score
      });

      await api.post('/patient/save_knowledge.php', {
        patient_id: user.id,
        procedure_id: procedureId,
        timepoint: `apt${apt}`,
        score: score,
        total: 3
      });

      setShowModal(true);
    } catch (err) {
      setError('Error saving score. Please submit again.');
      setLoading(false);
    }
  };

  const title = apt === 4 ? t('final_title') : t(`quiz${apt}_title`);
  const subtitle = apt === 4 ? t('final_subtitle') : t('apt1_subtitle');
  const q1Text = apt === 4 ? t('final_q1') : t(`quiz${apt}_q1`);
  const q2Text = apt === 4 ? t('final_q2') : t(`quiz${apt}_q2`);
  const q3Text = apt === 4 ? t('final_q3') : t(`quiz${apt}_q3`);

  const getOptions = (questionNum) => {
    const qKey = `q${questionNum}`;
    if (apt === 4) {
      return [t(`final_${qKey}_a`), t(`final_${qKey}_b`), t(`final_${qKey}_c`)];
    }
    return [
      t(`quiz${apt}_${qKey}_a`),
      t(`quiz${apt}_${qKey}_b`),
      t(`quiz${apt}_${qKey}_c`)
    ];
  };

  const getModalConfig = () => {
    if (finalScore === 3) {
      return { emoji: '🎉', message: t('quiz_msg_excellent') };
    } else if (finalScore === 2) {
      return { emoji: '👍', message: t('quiz_msg_good') };
    } else if (finalScore === 1) {
      return { emoji: '📖', message: t('quiz_msg_keep') };
    }
    return { emoji: '💪', message: t('quiz_msg_retry') };
  };

  const handleModalContinue = () => {
    setShowModal(false);
    if (apt === 1) {
      navigate('/patient/counselling');
    } else if (apt === 2) {
      navigate('/patient/postop');
    } else {
      navigate('/');
    }
  };

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{title}</h3>
        <p className="survey-subtitle">{subtitle}</p>

        {error && (
          <div className="auth-error">
            <AlertCircle size={20} />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit}>
          {/* Question 1 */}
          <div className="survey-question">
            <label className="question-label">{q1Text}</label>
            <div className="options-list">
              {getOptions(1).map((optText, idx) => (
                <label
                  key={idx}
                  className={`option-item ${answers.q1 === idx ? 'checked' : ''}`}
                >
                  <input
                    type="radio"
                    name="q1"
                    value={idx}
                    checked={answers.q1 === idx}
                    onChange={() => handleSelect('q1', idx)}
                    required
                  />
                  <span>{optText}</span>
                </label>
              ))}
            </div>
          </div>

          {/* Question 2 */}
          <div className="survey-question">
            <label className="question-label">{q2Text}</label>
            <div className="options-list">
              {getOptions(2).map((optText, idx) => (
                <label
                  key={idx}
                  className={`option-item ${answers.q2 === idx ? 'checked' : ''}`}
                >
                  <input
                    type="radio"
                    name="q2"
                    value={idx}
                    checked={answers.q2 === idx}
                    onChange={() => handleSelect('q2', idx)}
                    required
                  />
                  <span>{optText}</span>
                </label>
              ))}
            </div>
          </div>

          {/* Question 3 */}
          <div className="survey-question">
            <label className="question-label">{q3Text}</label>
            <div className="options-list">
              {getOptions(3).map((optText, idx) => (
                <label
                  key={idx}
                  className={`option-item ${answers.q3 === idx ? 'checked' : ''}`}
                >
                  <input
                    type="radio"
                    name="q3"
                    value={idx}
                    checked={answers.q3 === idx}
                    onChange={() => handleSelect('q3', idx)}
                    required
                  />
                  <span>{optText}</span>
                </label>
              ))}
            </div>
          </div>

          <button type="submit" className="survey-submit-btn" disabled={loading}>
            <span>{loading ? t('loading') : (apt === 4 ? (t('complete_assessment') || 'Complete') : t('submit'))}</span>
            <Check size={18} />
          </button>
        </form>
      </div>

      {showModal && (
        <div className="modal-overlay">
          <div className="modal-card">
            <div className="modal-emoji">{getModalConfig().emoji}</div>
            <h4 className="modal-title">{t('quiz_result_title')}</h4>
            <h5 className="modal-score">{t('quiz_score_label')}: {finalScore} / 3</h5>
            <p className="modal-message">{getModalConfig().message}</p>
            <button
              type="button"
              onClick={handleModalContinue}
              className="modal-btn"
            >
              {t('quiz_continue')}
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
