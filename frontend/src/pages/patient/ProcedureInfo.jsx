import React from 'react';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowRight, Info } from 'lucide-react';

export default function ProcedureInfo() {
  const { t } = useLanguage();
  const navigate = useNavigate();
  const { aptId } = useParams();
  const apt = parseInt(aptId) || 1;

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">{t('procedure_info_title')}</h3>
        <p className="survey-subtitle">{t('procedure_subtitle')}</p>

        <div className="info-block">
          <div className="info-item">
            <h5 className="info-heading"><Info size={16} className="text-primary-custom" /> {t('procedure_info_h1')}</h5>
            <p className="info-text">{t('procedure_info_p1')}</p>
          </div>
          <div className="info-item">
            <h5 className="info-heading"><Info size={16} className="text-primary-custom" /> {t('procedure_info_h2')}</h5>
            <p className="info-text">{t('procedure_info_p2')}</p>
          </div>
        </div>

        <button
          type="button"
          onClick={() => navigate(`/patient/education/${apt}`)}
          className="survey-submit-btn"
        >
          <span>{t('next')}</span>
          <ArrowRight size={18} />
        </button>
      </div>
    </div>
  );
}
