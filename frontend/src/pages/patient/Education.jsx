import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowRight, BookOpen } from 'lucide-react';

export default function Education() {
  const { user } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();
  const { aptId } = useParams();
  const apt = parseInt(aptId) || 1;

  const [loading, setLoading] = useState(true);
  const [eduContent, setEduContent] = useState(null);

  useEffect(() => {
    if (apt === 4) {
      setEduContent({
        title: t('reinforcement_title'),
        h1: t('reinforcement_h1'),
        p1: t('reinforcement_p1'),
        h2: t('reinforcement_h2'),
        p2: t('reinforcement_p2'),
        h3: t('reinforcement_h3'),
        p3: t('reinforcement_p3')
      });
      setLoading(false);
    } else {
      setEduContent({
        title: t(`edu${apt}_title`),
        h1: t(`edu${apt}_h1`),
        p1: t(`edu${apt}_p1`),
        h2: t(`edu${apt}_h2`),
        p2: t(`edu${apt}_p2`),
        h3: t(`edu${apt}_h3`),
        p3: t(`edu${apt}_p3`)
      });
      setLoading(false);
    }
  }, [apt, t]);

  if (loading) {
    return (
      <div className="flex-center full-screen">
        <div className="loading-spinner"></div>
      </div>
    );
  }

  const handleNext = () => {
    if (apt === 4) {
      navigate('/patient/quiz/4');
    } else {
      navigate(`/patient/anxiety/${apt}`);
    }
  };

  return (
    <div className="patient-container">
      <div className="survey-card">
        <h3 className="survey-title">
          <BookOpen className="inline-icon" size={24} style={{ marginRight: '8px', verticalAlign: 'middle' }} /> 
          {eduContent.title}
        </h3>
        <p className="survey-subtitle">{t('education')}</p>

        <div className="info-block">
          <div className="info-item">
            <h5 className="info-heading">{eduContent.h1}</h5>
            <p className="info-text whitespace-pre-wrap">{eduContent.p1}</p>
          </div>

          <div className="info-item">
            <h5 className="info-heading">{eduContent.h2}</h5>
            <p className="info-text whitespace-pre-wrap">{eduContent.p2}</p>
          </div>

          <div className="info-item">
            <h5 className="info-heading">{eduContent.h3}</h5>
            <p className="info-text whitespace-pre-wrap">{eduContent.p3}</p>
          </div>
        </div>

        <button
          type="button"
          onClick={handleNext}
          className="survey-submit-btn"
        >
          <span>{apt === 4 ? (t('final_assessment_btn') || 'Final Assessment') : t('take_quiz')}</span>
          <ArrowRight size={18} />
        </button>
      </div>
    </div>
  );
}
