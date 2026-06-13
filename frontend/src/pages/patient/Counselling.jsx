import React from 'react';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate } from 'react-router-dom';
import { ArrowRight, HelpCircle } from 'lucide-react';

export default function Counselling() {
  const { t } = useLanguage();
  const navigate = useNavigate();

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{t('counselling_title')}</h3>
        <p className="survey-subtitle">{t('counselling_h1')}</p>

        <div className="info-block">
          <div className="info-item">
            <h5 className="info-heading"><HelpCircle size={16} className="text-primary-custom" /> {t('counselling_h2')}</h5>
            <p className="info-text">{t('counselling_p2')}</p>
          </div>
          <div className="info-item">
            <h5 className="info-heading"><HelpCircle size={16} className="text-primary-custom" /> {t('counselling_h3')}</h5>
            <p className="info-text">{t('counselling_p3')}</p>
          </div>
        </div>

        <button
          type="button"
          onClick={() => navigate('/patient/consent')}
          className="survey-submit-btn"
        >
          <span>{t('counselling_btn') || 'Proceed to Consent'}</span>
          <ArrowRight size={18} />
        </button>
      </div>
    </div>
  );
}
