import React from 'react';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate } from 'react-router-dom';
import { ArrowRight } from 'lucide-react';

export default function PostOp() {
  const { t } = useLanguage();
  const navigate = useNavigate();

  const instructions = [
    t('postop_1'),
    t('postop_2'),
    t('postop_3'),
    t('postop_4'),
    t('postop_5')
  ];

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{t('postop_title')}</h3>
        <p className="survey-subtitle">{t('postop_subtitle')}</p>

        <div className="info-block gap-3">
          {instructions.map((instText, idx) => (
            <div key={idx} className="postop-instruction-item">
              <span className="postop-number">{idx + 1}</span>
              <p className="postop-text">{instText}</p>
            </div>
          ))}
        </div>

        <button
          type="button"
          onClick={() => navigate('/')}
          className="survey-submit-btn"
        >
          <span>{t('postop_next') || 'Proceed to Appointment 3'}</span>
          <ArrowRight size={18} />
        </button>
      </div>
    </div>
  );
}
