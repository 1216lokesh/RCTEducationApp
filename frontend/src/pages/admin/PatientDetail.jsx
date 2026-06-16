import React, { useEffect, useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { 
  ArrowLeft, 
  User, 
  Mail, 
  Phone, 
  Calendar, 
  Activity, 
  Key, 
  CheckSquare, 
  AlertCircle, 
  Save, 
  FileText, 
  HelpCircle,
  Award,
  TrendingUp,
  Heart,
  Smile,
  CheckCircle,
  FileCheck,
  LogOut,
  Users
} from 'lucide-react';
import api from '../../services/api';

export default function AdminPatientDetail() {
  const { id } = useParams();
  const { logout } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();

  // Data states
  const [data, setData] = useState(null);
  const [procedures, setProcedures] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Form states
  const [selectedProcedure, setSelectedProcedure] = useState('');
  const [selectedGroup, setSelectedGroup] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);

  // Actions status states
  const [assignStatus, setAssignStatus] = useState({ success: null, message: '' });
  const [pwdStatus, setPwdStatus] = useState({ success: null, message: '' });
  const [updateStatus, setUpdateStatus] = useState({ success: null, message: '' });

  const fetchPatientDetails = async () => {
    try {
      const detailRes = await api.get(`/admin/get_patient_details.php?id=${id}`);
      if (detailRes.data.success) {
        setData(detailRes.data);
        if (detailRes.data.assignedProcedure) {
          setSelectedProcedure(detailRes.data.assignedProcedure.procedure_id);
          setSelectedGroup(detailRes.data.assignedProcedure.group_type);
        }
      } else {
        setError(detailRes.data.message || 'Failed to retrieve patient details.');
      }
      
      const procRes = await api.get('/admin/get_procedures.php');
      setProcedures(Array.isArray(procRes.data) ? procRes.data : []);
    } catch (err) {
      console.error('Error fetching patient details:', err);
      setError('A connection error occurred while loading patient profile details.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    // eslint-disable-next-line react-hooks/exhaustive-deps, react-hooks/set-state-in-effect
    fetchPatientDetails();
  }, [id]);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const handleAssignProcedure = async (e) => {
    e.preventDefault();
    setAssignStatus({ success: null, message: '' });
    try {
      const payload = {
        patient_id: parseInt(id),
        procedure_id: parseInt(selectedProcedure),
        assigned_by: 1, // Defaulting admin id, normally parsed from session profile
        group_type: selectedGroup
      };
      
      const res = await api.post('/admin/assign_procedure.php', payload);
      if (res.data.status === 'success') {
        setAssignStatus({ success: true, message: 'Clinical procedure assigned successfully!' });
        fetchPatientDetails(); // Reload data
      } else {
        setAssignStatus({ success: false, message: res.data.message || 'Failed to update assignment.' });
      }
    } catch (err) {
      console.error('Error assigning procedure:', err);
      setAssignStatus({ success: false, message: 'An API error occurred.' });
    }
  };

  const handleResetPassword = async (e) => {
    e.preventDefault();
    setPwdStatus({ success: null, message: '' });
    try {
      const payload = {
        user_id: parseInt(id),
        new_password: newPassword
      };
      const res = await api.post('/admin/reset_password.php', payload);
      if (res.data.status === 'success') {
        setPwdStatus({ success: true, message: 'Password updated successfully!' });
        setNewPassword('');
      } else {
        setPwdStatus({ success: false, message: res.data.message || 'Failed to reset password.' });
      }
    } catch (err) {
      console.error('Error resetting password:', err);
      setPwdStatus({ success: false, message: 'Password reset API error.' });
    }
  };

  const handleToggleAttendance = async (aptCode) => {
    setUpdateStatus({ success: null, message: '' });
    try {
      const res = await api.post('/admin/save_attendance.php', {
        user_id: parseInt(id),
        apt: aptCode
      });
      if (res.data.status === 'success') {
        fetchPatientDetails(); // refresh details
      } else {
        setUpdateStatus({ success: false, message: 'Failed to log presence.' });
      }
    } catch (err) {
      console.error('Attendance toggle error:', err);
    }
  };

  const handleSaveConsent = async (status) => {
    setUpdateStatus({ success: null, message: '' });
    try {
      const res = await api.post('/admin/save_consent.php', {
        user_id: parseInt(id),
        consent_given: status
      });
      if (res.data.status === 'success') {
        fetchPatientDetails();
      } else {
        setUpdateStatus({ success: false, message: 'Failed to log consent status.' });
      }
    } catch (err) {
      console.error('Consent save error:', err);
    }
  };

  const handleSaveScore = async (quizName, scoreValue) => {
    setUpdateStatus({ success: null, message: '' });
    try {
      const res = await api.post('/admin/save_score.php', {
        user_id: parseInt(id),
        quiz: quizName,
        score: parseInt(scoreValue)
      });
      if (res.data.status === 'success') {
        fetchPatientDetails();
      } else {
        setUpdateStatus({ success: false, message: 'Failed to record quiz score.' });
      }
    } catch (err) {
      console.error('Score save error:', err);
    }
  };

  if (loading) {
    return (
      <div className="flex-center full-screen">
        <div className="loading-spinner"></div>
        <p className="mt-2 text-muted">{t('loading') || 'Loading patient records...'}</p>
      </div>
    );
  }

  if (error || !data) {
    return (
      <div className="flex-center full-screen text-center">
        <div className="card shadow-sm p-4 text-danger" style={{ maxWidth: '400px' }}>
          <h4>Error</h4>
          <p>{error || 'Patient data could not be parsed.'}</p>
          <Link to="/admin/patients" className="btn btn-primary mt-2">Back to Registry</Link>
        </div>
      </div>
    );
  }

  const { patient, assignedProcedure, consent, scores, attendance, baseline, anxiety, satisfaction } = data;

  return (
    <div className="admin-container">
      {/* Sidebar */}
      <aside className="admin-sidebar">
        <div className="sidebar-brand">
          <Activity size={24} className="text-primary" />
          <span>RCT Admin Panel</span>
        </div>
        
        <nav className="sidebar-nav">
          <Link to="/admin" className="nav-item">
            <TrendingUp size={18} />
            <span>Dashboard</span>
          </Link>
          <Link to="/admin/patients" className="nav-item active">
            <Users size={18} />
            <span>Patient Registry</span>
          </Link>
          <button type="button" onClick={handleLogout} className="nav-item logout-btn-item">
            <LogOut size={18} />
            <span>Logout</span>
          </button>
        </nav>

        <div className="sidebar-profile">
          <div className="avatar">AD</div>
          <div className="profile-details">
            <h6>Administrator</h6>
            <span>Admin Portal</span>
          </div>
        </div>
      </aside>

      {/* Main Container */}
      <main className="admin-main-content">
        <header className="admin-header">
          <div className="d-flex align-items-center gap-3">
            <Link to="/admin/patients" className="back-btn flex-center">
              <ArrowLeft size={20} />
            </Link>
            <div>
              <h1 className="h3 fw-bold text-dark mb-0">Patient: {patient.name}</h1>
              <p className="text-muted small mb-0">Assess clinical outcomes, scores logs, timelines and procedures</p>
            </div>
          </div>
        </header>

        {updateStatus.message && (
          <div className="alert alert-danger mt-3 flex-center gap-2">
            <AlertCircle size={16} />
            <span>{updateStatus.message}</span>
          </div>
        )}

        <div className="patient-detail-grid mt-4">
          
          {/* Left Column: Profile, Assign procedure, Reset password */}
          <div className="detail-column-left">
            
            {/* Profile Overview Card */}
            <div className="card shadow-sm border-0 p-4 mb-4">
              <h5 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                <User size={18} />
                <span>Profile Card</span>
              </h5>
              
              <div className="profile-meta-list">
                <div className="meta-item flex-center gap-2 mb-2">
                  <Mail size={16} className="text-muted" />
                  <span className="text-dark">{patient.email}</span>
                </div>
                <div className="meta-item flex-center gap-2 mb-2">
                  <Phone size={16} className="text-muted" />
                  <span className="text-dark">{patient.phone || 'No phone registered'}</span>
                </div>
                <div className="meta-item flex-center gap-2 mb-2">
                  <Calendar size={16} className="text-muted" />
                  <span className="text-dark">Joined: {new Date(patient.created_at).toLocaleDateString()}</span>
                </div>
                <div className="meta-item flex-center gap-2">
                  <Activity size={16} className="text-muted" />
                  <span className={`badge ${patient.status === 'active' ? 'bg-success' : 'bg-secondary'}`}>
                    {(patient.status || 'Active').toUpperCase()}
                  </span>
                </div>
              </div>
            </div>

            {/* Assign Procedure Card */}
            <div className="card shadow-sm border-0 p-4 mb-4">
              <h5 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                <FileText size={18} />
                <span>Assign Procedure</span>
              </h5>

              {assignedProcedure ? (
                <div className="alert alert-info border-0 p-3 mb-3 bg-gradient-blue text-white" style={{ borderRadius: '6px' }}>
                  <h6 className="fw-bold mb-1">Assigned Procedure:</h6>
                  <p className="mb-0 fw-semibold">{assignedProcedure.name}</p>
                  <span className="d-block small mt-1">Group: <strong>{assignedProcedure.group_type}</strong></span>
                  <span className="d-block small opacity-75">Assigned on: {new Date(assignedProcedure.assigned_date).toLocaleDateString()}</span>
                </div>
              ) : (
                <div className="alert alert-warning border-0 p-3 mb-3 flex-center gap-2">
                  <AlertCircle size={16} />
                  <span>No treatment plan or clinical group assigned yet.</span>
                </div>
              )}

              <form onSubmit={handleAssignProcedure}>
                <div className="mb-3">
                  <label className="form-label text-dark fw-semibold small">Clinical Procedure</label>
                  <select 
                    className="form-select form-control"
                    value={selectedProcedure}
                    onChange={(e) => setSelectedProcedure(e.target.value)}
                    required
                  >
                    <option value="">-- Choose Procedure --</option>
                    {procedures.map((proc) => (
                      <option key={proc.id} value={proc.id}>
                        {proc.name} ({proc.category})
                      </option>
                    ))}
                  </select>
                </div>

                <div className="mb-3">
                  <label className="form-label text-dark fw-semibold small">Clinical Study Group</label>
                  <select 
                    className="form-select form-control"
                    value={selectedGroup}
                    onChange={(e) => setSelectedGroup(e.target.value)}
                    required
                  >
                    <option value="">-- Choose Group --</option>
                    <option value="Intervention">Intervention (Mobile App Education)</option>
                    <option value="Comparator">Comparator (Standard Care)</option>
                  </select>
                </div>

                <button type="submit" className="btn btn-primary w-100 flex-center gap-2 bg-primary-custom">
                  <Save size={16} />
                  <span>Update Assignment</span>
                </button>
              </form>

              {assignStatus.message && (
                <div className={`alert mt-3 ${assignStatus.success ? 'alert-success' : 'alert-danger'}`}>
                  {assignStatus.message}
                </div>
              )}
            </div>

            {/* Reset Password Card */}
            <div className="card shadow-sm border-0 p-4">
              <h5 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                <Key size={18} />
                <span>Reset Password</span>
              </h5>

              <form onSubmit={handleResetPassword}>
                <div className="mb-3">
                  <label className="form-label text-dark fw-semibold small">New Password</label>
                  <div className="input-group">
                    <input 
                      type={showPassword ? "text" : "password"} 
                      className="form-control" 
                      placeholder="Enter new password"
                      value={newPassword}
                      onChange={(e) => setNewPassword(e.target.value)}
                      required 
                    />
                    <button 
                      type="button" 
                      className="btn btn-outline-secondary"
                      onClick={() => setShowPassword(!showPassword)}
                    >
                      {showPassword ? "Hide" : "Show"}
                    </button>
                  </div>
                </div>

                <button type="submit" className="btn btn-danger w-100 flex-center gap-2">
                  <Key size={16} />
                  <span>Reset Password</span>
                </button>
              </form>

              {pwdStatus.message && (
                <div className={`alert mt-3 ${pwdStatus.success ? 'alert-success' : 'alert-danger'}`}>
                  {pwdStatus.message}
                </div>
              )}
            </div>

          </div>

          {/* Right Column: Outcomes, Timeline & Questionnaire details */}
          <div className="detail-column-right">
            
            {/* Interactive Journey Trackers */}
            <div className="detail-row-flex mb-4">
              {/* Quiz scores interactive logs */}
              <div className="card shadow-sm border-0 p-4 flex-1">
                <h5 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                  <Award size={18} />
                  <span>Quiz Scores</span>
                </h5>
                <div className="scores-toggles">
                  {/* Quiz 1 */}
                  <div className="toggle-score-item flex-between mb-3 border-bottom pb-2">
                    <span className="text-dark small fw-semibold">Appointment 1 Quiz score:</span>
                    <select 
                      className="form-select form-select-sm score-selector"
                      value={scores && scores.quiz1 !== null ? scores.quiz1 : ''}
                      onChange={(e) => handleSaveScore('quiz1', e.target.value)}
                    >
                      <option value="">Not Taken</option>
                      <option value="0">0 / 3</option>
                      <option value="1">1 / 3</option>
                      <option value="2">2 / 3</option>
                      <option value="3">3 / 3</option>
                    </select>
                  </div>

                  {/* Quiz 2 */}
                  <div className="toggle-score-item flex-between mb-3 border-bottom pb-2">
                    <span className="text-dark small fw-semibold">Appointment 2 Quiz score:</span>
                    <select 
                      className="form-select form-select-sm score-selector"
                      value={scores && scores.quiz2 !== null ? scores.quiz2 : ''}
                      onChange={(e) => handleSaveScore('quiz2', e.target.value)}
                    >
                      <option value="">Not Taken</option>
                      <option value="0">0 / 3</option>
                      <option value="1">1 / 3</option>
                      <option value="2">2 / 3</option>
                      <option value="3">3 / 3</option>
                    </select>
                  </div>

                  {/* Quiz 3 */}
                  <div className="toggle-score-item flex-between mb-3 border-bottom pb-2">
                    <span className="text-dark small fw-semibold">Appointment 3 Quiz score:</span>
                    <select 
                      className="form-select form-select-sm score-selector"
                      value={scores && scores.quiz3 !== null ? scores.quiz3 : ''}
                      onChange={(e) => handleSaveScore('quiz3', e.target.value)}
                    >
                      <option value="">Not Taken</option>
                      <option value="0">0 / 3</option>
                      <option value="1">1 / 3</option>
                      <option value="2">2 / 3</option>
                      <option value="3">3 / 3</option>
                    </select>
                  </div>

                  {/* Follow Up Quiz */}
                  <div className="toggle-score-item flex-between mb-2">
                    <span className="text-dark small fw-semibold">Follow Up Quiz complete:</span>
                    <select 
                      className="form-select form-select-sm score-selector"
                      value={scores && scores.followup_1week !== null ? scores.followup_1week : ''}
                      onChange={(e) => handleSaveScore('followup_1week', e.target.value)}
                    >
                      <option value="">Not Taken</option>
                      <option value="1">Yes (Done)</option>
                    </select>
                  </div>
                </div>
              </div>

              {/* Attendance Tracker */}
              <div className="card shadow-sm border-0 p-4 flex-1">
                <h5 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                  <CheckSquare size={18} />
                  <span>Attendance Records</span>
                </h5>
                
                <div className="attendance-list">
                  <div className="attendance-toggle-row flex-between mb-3 border-bottom pb-2">
                    <span className="small text-dark fw-semibold">Appointment 1 (Diagnosis)</span>
                    <button 
                      type="button" 
                      className={`btn btn-sm ${attendance && attendance.apt1 === 'present' ? 'btn-success' : 'btn-outline-secondary'}`}
                      onClick={() => handleToggleAttendance('apt1')}
                    >
                      {attendance && attendance.apt1 === 'present' ? 'Present' : 'Mark Present'}
                    </button>
                  </div>

                  <div className="attendance-toggle-row flex-between mb-3 border-bottom pb-2">
                    <span className="small text-dark fw-semibold">Appointment 2 (RCT Procedure)</span>
                    <button 
                      type="button" 
                      className={`btn btn-sm ${attendance && attendance.apt2 === 'present' ? 'btn-success' : 'btn-outline-secondary'}`}
                      onClick={() => handleToggleAttendance('apt2')}
                    >
                      {attendance && attendance.apt2 === 'present' ? 'Present' : 'Mark Present'}
                    </button>
                  </div>

                  <div className="attendance-toggle-row flex-between mb-3 border-bottom pb-2">
                    <span className="small text-dark fw-semibold">Appointment 3 (Final Restoration)</span>
                    <button 
                      type="button" 
                      className={`btn btn-sm ${attendance && attendance.apt3 === 'present' ? 'btn-success' : 'btn-outline-secondary'}`}
                      onClick={() => handleToggleAttendance('apt3')}
                    >
                      {attendance && attendance.apt3 === 'present' ? 'Present' : 'Mark Present'}
                    </button>
                  </div>

                  <div className="attendance-toggle-row flex-between mb-2">
                    <span className="small text-dark fw-semibold">Follow Up Visit (1-Week)</span>
                    <button 
                      type="button" 
                      className={`btn btn-sm ${attendance && attendance.apt4 === 'present' ? 'btn-success' : 'btn-outline-secondary'}`}
                      onClick={() => handleToggleAttendance('apt4')}
                    >
                      {attendance && attendance.apt4 === 'present' ? 'Present' : 'Mark Present'}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            {/* Consent Signature Status */}
            <div className="card shadow-sm border-0 p-4 mb-4">
              <h5 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                <FileCheck size={18} />
                <span>Digital Consent Sign-off</span>
              </h5>
              
              <div className="flex-between">
                <div>
                  <span className="small text-muted d-block">Consent Signature:</span>
                  <span className="fw-bold text-dark">
                    {consent && consent.consent_given === 'yes' ? (
                      <span className="text-success flex-center gap-1 mt-1">
                        <CheckCircle size={16} />
                        <span>Signed ({new Date(consent.consent_date).toLocaleDateString()})</span>
                      </span>
                    ) : (
                      <span className="text-muted d-block mt-1">No consent signature captured</span>
                    )}
                  </span>
                </div>
                
                <div className="d-flex gap-2">
                  <button 
                    type="button" 
                    className={`btn btn-sm ${consent && consent.consent_given === 'yes' ? 'btn-success' : 'btn-outline-success'}`}
                    onClick={() => handleSaveConsent('yes')}
                  >
                    Consent Signed
                  </button>
                  <button 
                    type="button" 
                    className={`btn btn-sm ${!consent || consent.consent_given !== 'yes' ? 'btn-secondary' : 'btn-outline-secondary'}`}
                    onClick={() => handleSaveConsent('no')}
                  >
                    Reset Consent
                  </button>
                </div>
              </div>
            </div>

            {/* Baseline Answers Log */}
            <div className="card shadow-sm border-0 p-4 mb-4">
              <h5 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                <HelpCircle size={18} />
                <span>Baseline Questionnaire Answers</span>
              </h5>

              {baseline && baseline.length > 0 ? (
                <div className="table-responsive">
                  <table className="table table-hover align-middle mb-0" style={{ fontSize: '0.85rem' }}>
                    <thead className="table-light">
                      <tr>
                        <th>Visit</th>
                        <th>Q1 (Expectations)</th>
                        <th>Q2 (Treatment pain fear)</th>
                        <th>Q3 (Information clarity)</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      {baseline.map((resp, idx) => (
                        <tr key={idx}>
                          <td className="fw-semibold text-dark">
                            {resp.appointment === 'apt1' ? 'Apt 1' : resp.appointment === 'apt2' ? 'Apt 2' : resp.appointment === 'apt3' ? 'Apt 3' : 'Follow Up'}
                          </td>
                          <td>{resp.q1}</td>
                          <td>{resp.q2}</td>
                          <td>{resp.q3}</td>
                          <td>{new Date(resp.created_at).toLocaleDateString()}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <p className="text-muted small mb-0"><AlertCircle size={14} className="inline me-1" /> No baseline survey responses logged yet.</p>
              )}
            </div>

            {/* Outcomes charts / lists */}
            <div className="detail-row-flex">
              {/* Anxiety score history */}
              <div className="card shadow-sm border-0 p-4 flex-1">
                <h6 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                  <Heart size={16} className="text-danger" />
                  <span>Anxiety score history</span>
                </h6>
                
                {anxiety && anxiety.length > 0 ? (
                  <ul className="list-group list-group-flush" style={{ fontSize: '0.85rem' }}>
                    {anxiety.map((anx, idx) => (
                      <li key={idx} className="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                        <span className="text-muted">
                          {anx.timepoint === 'apt1' ? 'Apt 1' : anx.timepoint === 'apt2' ? 'Apt 2' : anx.timepoint === 'apt3' ? 'Apt 3' : 'Follow Up'}
                        </span>
                        <span className={`badge rounded-pill text-white ${anx.score > 10 ? 'bg-danger' : anx.score > 5 ? 'bg-warning text-dark' : 'bg-success'}`} style={{ fontWeight: '500' }}>
                          Score: {anx.score} / 15
                        </span>
                      </li>
                    ))}
                  </ul>
                ) : (
                  <p className="text-muted small mb-0">No anxiety score records logged.</p>
                )}
              </div>

              {/* Satisfaction score history */}
              <div className="card shadow-sm border-0 p-4 flex-1">
                <h6 className="fw-bold text-primary-custom mb-3 flex-center gap-2">
                  <Smile size={16} className="text-success" />
                  <span>Satisfaction score history</span>
                </h6>

                {satisfaction && satisfaction.length > 0 ? (
                  <ul className="list-group list-group-flush" style={{ fontSize: '0.85rem' }}>
                    {satisfaction.map((sat, idx) => (
                      <li key={idx} className="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                        <span className="text-muted">
                          {sat.timepoint.replace('_', ' ').toUpperCase()}
                        </span>
                        <span className="badge rounded-pill bg-success text-white" style={{ fontWeight: '500' }}>
                          Score: {sat.score} / 25
                        </span>
                      </li>
                    ))}
                  </ul>
                ) : (
                  <p className="text-muted small mb-0">No satisfaction logs found.</p>
                )}
              </div>
            </div>

          </div>
        </div>
      </main>
    </div>
  );
}
