import React, { useEffect, useState } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { useNavigate } from 'react-router-dom';
import { FileText, LogOut, CheckCircle, ArrowRight, User, Activity, AlertTriangle } from 'lucide-react';
import api from '../../services/api';

export default function Dashboard() {
  const { user, logout } = useAuth();
  const { t, language, setLanguage } = useLanguage();
  const navigate = useNavigate();

  const [procedure, setProcedure] = useState(null);
  const [progressData, setProgressData] = useState({
    progress: 0,
    apt1_completed: false,
    apt2_completed: false,
    apt3_completed: false,
    followup_completed: false
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        // Fetch procedure
        const procRes = await api.post('/patient/get_my_procedure.php', { patient_id: user.id });
        if (procRes.data.status === 'success') {
          setProcedure(procRes.data.data);
        }
        
        // Fetch progress completions
        const progRes = await api.get('/patient/get_progress.php');
        if (progRes.data.success) {
          setProgressData({
            progress: progRes.data.progress,
            apt1_completed: progRes.data.apt1_completed,
            apt2_completed: progRes.data.apt2_completed,
            apt3_completed: progRes.data.apt3_completed,
            followup_completed: progRes.data.followup_completed
          });
        }
      } catch (err) {
        console.error('Error fetching dashboard data:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, [user.id]);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  if (loading) {
    return (
      <div className="flex-center full-screen">
        <div className="loading-spinner"></div>
        <p className="mt-2 text-muted">{t('loading')}</p>
      </div>
    );
  }

  const { progress, apt1_completed, apt2_completed, apt3_completed, followup_completed } = progressData;

  return (
    <div className="patient-container">
      {/* Top Navbar */}
      <div className="patient-header">
        <div className="header-user">
          <div className="avatar">
            <User size={20} />
          </div>
          <div>
            <h3 className="user-welcome">{t('welcome')}, {user.name}</h3>
            <p className="subtitle">{t('dashboard_subtitle') || 'Select your appointment below'}</p>
          </div>
        </div>
        <div className="lang-selector header-lang">
          {['en', 'ta', 'hi', 'te'].map((lang) => (
            <button
              key={lang}
              type="button"
              className={`lang-btn mini ${language === lang ? 'active' : ''}`}
              onClick={() => setLanguage(lang)}
            >
              {lang.toUpperCase()}
            </button>
          ))}
        </div>
      </div>

      {/* Main Layout Grid */}
      <div className="patient-grid">
        {/* Procedure Card */}
        <div className="procedure-card">
          {procedure ? (
            <>
              <div className="card-tag">
                <FileText size={18} />
                <span>{t('procedure_title') || 'Procedure'}</span>
              </div>
              <h4 className="proc-title">{procedure.procedure_name}</h4>
              <p className="proc-meta">{t('procedure_category') || 'Category'}: {procedure.category}</p>
              <p className="proc-desc">{procedure.description}</p>
              <div className="group-info">
                <Activity size={16} />
                <span>
                  {t('procedure_group') || 'Your Group'}:{' '}
                  {procedure.group_type.toLowerCase() === 'intervention'
                    ? (t('procedure_intervention') || 'Intervention Group')
                    : (t('procedure_comparator') || 'Standard Care Group')}
                </span>
              </div>
            </>
          ) : (
            <div className="no-proc">
              <AlertTriangle size={24} className="text-warning" />
              <h5>{t('procedure_not_assigned') || 'No procedure assigned yet'}</h5>
              <p>Please contact your dentist to assign your procedure treatment plan.</p>
            </div>
          )}
        </div>

        {/* Progress Card */}
        <div className="progress-card">
          <div className="progress-header">
            <h5>Your Progress</h5>
            <span className="progress-count">{progress} / 4 Completed</span>
          </div>
          <div className="progress-bar-container">
            <div className="progress-bar-fill" style={{ width: `${progress * 25}%` }}></div>
          </div>
        </div>

        {/* Journey Options */}
        <div className="journey-steps">
          {/* Step 1 */}
          <div className="journey-step-container">
            <span className="step-label">{t('apt1_label') || 'Appointment 1 — Diagnosis'}</span>
            <button
              onClick={() => navigate('/patient/baseline/1')}
              disabled={apt1_completed}
              className={`journey-btn ${apt1_completed ? 'completed' : 'primary'}`}
            >
              <span>{t('appointment1') || 'Start Appointment 1'}</span>
              {apt1_completed ? <CheckCircle size={18} /> : <ArrowRight size={18} />}
            </button>
          </div>

          {/* Step 2 */}
          <div className="journey-step-container">
            <span className="step-label">{t('apt2_label') || 'Appointment 2 — Root Canal Procedure'}</span>
            <button
              onClick={() => navigate('/patient/baseline/2')}
              disabled={apt2_completed || !apt1_completed}
              className={`journey-btn ${apt2_completed ? 'completed' : 'primary'}`}
            >
              <span>{t('appointment2') || 'Start Appointment 2'}</span>
              {apt2_completed ? <CheckCircle size={18} /> : <ArrowRight size={18} />}
            </button>
          </div>

          {/* Step 3 */}
          <div className="journey-step-container">
            <span className="step-label">{t('apt3_label') || 'Appointment 3 — Crown / Final Restoration'}</span>
            <button
              onClick={() => navigate('/patient/baseline/3')}
              disabled={apt3_completed || !apt2_completed}
              className={`journey-btn ${apt3_completed ? 'completed' : 'primary'}`}
            >
              <span>{t('appointment3') || 'Start Appointment 3'}</span>
              {apt3_completed ? <CheckCircle size={18} /> : <ArrowRight size={18} />}
            </button>
          </div>

          {/* Step 4 */}
          <div className="journey-step-container">
            <span className="step-label">{t('followup_label') || 'Follow Up Visit'}</span>
            <button
              onClick={() => navigate('/patient/baseline/4')}
              disabled={followup_completed || !apt3_completed}
              className={`journey-btn info-btn ${followup_completed ? 'completed' : ''}`}
            >
              <span>{t('followup') || 'Start Follow Up'}</span>
              {followup_completed ? <CheckCircle size={18} /> : <ArrowRight size={18} />}
            </button>
          </div>
        </div>

        {/* Logout Button */}
        <button type="button" onClick={handleLogout} className="logout-btn">
          <LogOut size={18} />
          <span>{t('logout')}</span>
        </button>
      </div>
    </div>
  );
}
