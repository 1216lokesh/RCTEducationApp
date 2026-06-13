import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { 
  Users, 
  Search, 
  ChevronRight, 
  TrendingUp, 
  LogOut, 
  FileSpreadsheet, 
  FileCheck, 
  ClipboardCheck, 
  Download, 
  Eye, 
  RefreshCw,
  AlertCircle
} from 'lucide-react';
import api from '../../services/api';

export default function AdminPatients() {
  const { logout } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();

  const [activeTab, setActiveTab] = useState('patients'); // patients, scores, consent, attendance, export
  const [searchQuery, setSearchQuery] = useState('');
  
  // Tab states
  const [patients, setPatients] = useState([]);
  const [scores, setScores] = useState([]);
  const [consents, setConsents] = useState([]);
  const [attendance, setAttendance] = useState([]);
  
  // Loading & error states
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  
  // Export process state
  const [exporting, setExporting] = useState(false);
  const [exportMessage, setExportMessage] = useState('');

  // Fetch data depending on active tab
  const fetchData = async (tab) => {
    setLoading(true);
    setError(null);
    try {
      if (tab === 'patients') {
        const res = await api.get('/admin/get_patients.php');
        setPatients(Array.isArray(res.data) ? res.data : []);
      } else if (tab === 'scores') {
        const res = await api.get('/admin/get_scores.php');
        setScores(Array.isArray(res.data) ? res.data : []);
      } else if (tab === 'consent') {
        const res = await api.get('/admin/get_consent.php');
        setConsents(Array.isArray(res.data) ? res.data : []);
      } else if (tab === 'attendance') {
        const res = await api.get('/admin/get_attendance.php');
        setAttendance(Array.isArray(res.data) ? res.data : []);
      }
    } catch (err) {
      console.error(`Error fetching data for ${tab}:`, err);
      setError(`Failed to load ${tab} data. Please try again.`);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (activeTab !== 'export') {
      fetchData(activeTab);
    }
  }, [activeTab]);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const handleExport = async () => {
    setExporting(true);
    setExportMessage('Generating CSV Export on server...');
    try {
      const res = await api.get('/admin/export.php');
      if (res.data.status === 'success') {
        // Trigger download of the generated CSV file
        const downloadUrl = `/api/exports/${res.data.file}`;
        
        // Create an anchor element to trigger download
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.setAttribute('download', res.data.file);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        setExportMessage('Export downloaded successfully!');
      } else {
        setExportMessage('Failed to export data.');
      }
    } catch (err) {
      console.error('Error exporting data:', err);
      setExportMessage('Export failed due to network error.');
    } finally {
      setExporting(false);
    }
  };

  // Local client-side filters
  const filteredPatients = patients.filter(p => 
    p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    p.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
    (p.phone && p.phone.includes(searchQuery))
  );

  const filteredScores = scores.filter(s => 
    s.name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const filteredConsents = consents.filter(c => 
    c.name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const filteredAttendance = attendance.filter(a => 
    a.name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="admin-container">
      {/* Sidebar Navigation */}
      <aside className="admin-sidebar">
        <div className="sidebar-brand">
          <Users size={24} className="text-primary" />
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

      {/* Main Content Area */}
      <main className="admin-main-content">
        <header className="admin-header">
          <div>
            <h1 className="h2 fw-bold text-dark">Patient Management</h1>
            <p className="text-muted mb-0">Search registries, track timelines, check attendance, or download outputs.</p>
          </div>
          
          {/* Quick Refresh */}
          {activeTab !== 'export' && (
            <button 
              type="button"
              className="btn btn-outline-secondary btn-sm flex-center gap-1"
              onClick={() => fetchData(activeTab)}
              disabled={loading}
            >
              <RefreshCw size={14} className={loading ? 'spin' : ''} />
              <span>Refresh Registry</span>
            </button>
          )}
        </header>

        {/* Console View Tabs */}
        <div className="admin-tabs-nav mt-3">
          <button 
            type="button" 
            className={`admin-tab-link ${activeTab === 'patients' ? 'active' : ''}`}
            onClick={() => { setActiveTab('patients'); setSearchQuery(''); }}
          >
            <Users size={16} />
            <span>Patients Directory</span>
          </button>
          <button 
            type="button" 
            className={`admin-tab-link ${activeTab === 'scores' ? 'active' : ''}`}
            onClick={() => { setActiveTab('scores'); setSearchQuery(''); }}
          >
            <FileSpreadsheet size={16} />
            <span>Assessment Scores</span>
          </button>
          <button 
            type="button" 
            className={`admin-tab-link ${activeTab === 'consent' ? 'active' : ''}`}
            onClick={() => { setActiveTab('consent'); setSearchQuery(''); }}
          >
            <FileCheck size={16} />
            <span>Digital Consents</span>
          </button>
          <button 
            type="button" 
            className={`admin-tab-link ${activeTab === 'attendance' ? 'active' : ''}`}
            onClick={() => { setActiveTab('attendance'); setSearchQuery(''); }}
          >
            <ClipboardCheck size={16} />
            <span>Attendance Logs</span>
          </button>
          <button 
            type="button" 
            className={`admin-tab-link ${activeTab === 'export' ? 'active' : ''}`}
            onClick={() => { setActiveTab('export'); setSearchQuery(''); }}
          >
            <Download size={16} />
            <span>Data Export</span>
          </button>
        </div>

        {/* Search Input Filter */}
        {activeTab !== 'export' && (
          <div className="search-filter-box mt-4">
            <div className="input-icon-group">
              <Search className="search-icon" size={18} />
              <input 
                type="text" 
                placeholder="Search patient names, emails or phones..." 
                className="form-control search-control"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
              />
            </div>
          </div>
        )}

        {/* Tab Content Display */}
        <section className="tab-contents-section mt-4">
          <div className="card shadow-sm border-0">
            <div className="card-body p-0">
              
              {loading ? (
                <div className="flex-center p-5">
                  <div className="loading-spinner"></div>
                  <span className="ms-2 text-muted">Fetching registry records...</span>
                </div>
              ) : error ? (
                <div className="alert alert-danger m-4 flex-center gap-2">
                  <AlertCircle size={20} />
                  <span>{error}</span>
                </div>
              ) : (
                <>
                  {/* Patients Directory Tab */}
                  {activeTab === 'patients' && (
                    <div className="table-responsive">
                      <table className="table table-hover align-middle mb-0">
                        <thead className="table-light">
                          <tr>
                            <th className="ps-4">#</th>
                            <th>Patient Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Registered Date</th>
                            <th className="pe-4 text-end">Details</th>
                          </tr>
                        </thead>
                        <tbody>
                          {filteredPatients.length > 0 ? (
                            filteredPatients.map((patient, index) => (
                              <tr key={patient.id}>
                                <td className="ps-4 text-muted fw-semibold">{index + 1}</td>
                                <td className="fw-semibold text-dark">{patient.name}</td>
                                <td>{patient.email}</td>
                                <td>{patient.phone || 'No Phone'}</td>
                                <td>
                                  <span className={`badge ${patient.status === 'active' ? 'bg-success' : 'bg-secondary'}`} style={{ borderRadius: '4px' }}>
                                    {patient.status ? patient.status.toUpperCase() : 'ACTIVE'}
                                  </span>
                                </td>
                                <td>{patient.created_at ? new Date(patient.created_at).toLocaleDateString() : 'N/A'}</td>
                                <td className="pe-4 text-end">
                                  <Link 
                                    to={`/admin/patient/${patient.id}`} 
                                    className="btn btn-sm text-white px-3 flex-center gap-1 float-end bg-primary-custom"
                                    style={{ borderRadius: '6px', width: 'fit-content' }}
                                  >
                                    <Eye size={14} />
                                    <span>Profile</span>
                                  </Link>
                                </td>
                              </tr>
                            ))
                          ) : (
                            <tr>
                              <td colSpan="7" className="text-center p-4 text-muted">No patient records found matching query.</td>
                            </tr>
                          )}
                        </tbody>
                      </table>
                    </div>
                  )}

                  {/* Assessment Scores Tab */}
                  {activeTab === 'scores' && (
                    <div className="table-responsive">
                      <table className="table table-hover align-middle mb-0">
                        <thead className="table-light">
                          <tr>
                            <th className="ps-4">#</th>
                            <th>Patient Name</th>
                            <th>Quiz 1 Score</th>
                            <th>Quiz 2 Score</th>
                            <th>Quiz 3 Score</th>
                            <th>Follow Up Score</th>
                          </tr>
                        </thead>
                        <tbody>
                          {filteredScores.length > 0 ? (
                            filteredScores.map((row, index) => (
                              <tr key={index}>
                                <td className="ps-4 text-muted fw-semibold">{index + 1}</td>
                                <td className="fw-semibold text-dark">{row.name}</td>
                                <td>{row.quiz1 !== null ? `${row.quiz1} / 3` : <span className="text-muted">-</span>}</td>
                                <td>{row.quiz2 !== null ? `${row.quiz2} / 3` : <span className="text-muted">-</span>}</td>
                                <td>{row.quiz3 !== null ? `${row.quiz3} / 3` : <span className="text-muted">-</span>}</td>
                                <td>{row.followup_1week !== null ? `${row.followup_1week} / 3` : <span className="text-muted">-</span>}</td>
                              </tr>
                            ))
                          ) : (
                            <tr>
                              <td colSpan="6" className="text-center p-4 text-muted">No assessment score records found.</td>
                            </tr>
                          )}
                        </tbody>
                      </table>
                    </div>
                  )}

                  {/* Digital Consents Tab */}
                  {activeTab === 'consent' && (
                    <div className="table-responsive">
                      <table className="table table-hover align-middle mb-0">
                        <thead className="table-light">
                          <tr>
                            <th className="ps-4">#</th>
                            <th>Patient Name</th>
                            <th>Consent Logged</th>
                            <th>Consent Signature Date</th>
                          </tr>
                        </thead>
                        <tbody>
                          {filteredConsents.length > 0 ? (
                            filteredConsents.map((row, index) => (
                              <tr key={index}>
                                <td className="ps-4 text-muted fw-semibold">{index + 1}</td>
                                <td className="fw-semibold text-dark">{row.name}</td>
                                <td>
                                  <span className={`badge ${row.consent_given === 'yes' ? 'bg-success' : 'bg-danger'}`} style={{ borderRadius: '4px' }}>
                                    {row.consent_given ? row.consent_given.toUpperCase() : 'NO'}
                                  </span>
                                </td>
                                <td>{row.consent_date ? new Date(row.consent_date).toLocaleDateString() : 'N/A'}</td>
                              </tr>
                            ))
                          ) : (
                            <tr>
                              <td colSpan="4" className="text-center p-4 text-muted">No consent signatures found.</td>
                            </tr>
                          )}
                        </tbody>
                      </table>
                    </div>
                  )}

                  {/* Attendance Logs Tab */}
                  {activeTab === 'attendance' && (
                    <div className="table-responsive">
                      <table className="table table-hover align-middle mb-0">
                        <thead className="table-light">
                          <tr>
                            <th className="ps-4">#</th>
                            <th>Patient Name</th>
                            <th>Appointment 1</th>
                            <th>Appointment 2</th>
                            <th>Appointment 3</th>
                            <th>Follow Up Visit</th>
                          </tr>
                        </thead>
                        <tbody>
                          {filteredAttendance.length > 0 ? (
                            filteredAttendance.map((row, index) => (
                              <tr key={index}>
                                <td className="ps-4 text-muted fw-semibold">{index + 1}</td>
                                <td className="fw-semibold text-dark">{row.name}</td>
                                <td>
                                  <span className={`badge ${row.apt1 === 'present' ? 'bg-success' : 'bg-secondary'}`} style={{ borderRadius: '4px' }}>
                                    {row.apt1 === 'present' ? 'PRESENT' : 'ABSENT'}
                                  </span>
                                </td>
                                <td>
                                  <span className={`badge ${row.apt2 === 'present' ? 'bg-success' : 'bg-secondary'}`} style={{ borderRadius: '4px' }}>
                                    {row.apt2 === 'present' ? 'PRESENT' : 'ABSENT'}
                                  </span>
                                </td>
                                <td>
                                  <span className={`badge ${row.apt3 === 'present' ? 'bg-success' : 'bg-secondary'}`} style={{ borderRadius: '4px' }}>
                                    {row.apt3 === 'present' ? 'PRESENT' : 'ABSENT'}
                                  </span>
                                </td>
                                <td>
                                  <span className={`badge ${row.apt4 === 'present' ? 'bg-success' : 'bg-secondary'}`} style={{ borderRadius: '4px' }}>
                                    {row.apt4 === 'present' ? 'PRESENT' : 'ABSENT'}
                                  </span>
                                </td>
                              </tr>
                            ))
                          ) : (
                            <tr>
                              <td colSpan="6" className="text-center p-4 text-muted">No attendance logs found.</td>
                            </tr>
                          )}
                        </tbody>
                      </table>
                    </div>
                  )}

                  {/* Data Export Tab */}
                  {activeTab === 'export' && (
                    <div className="p-5 text-center">
                      <div className="export-icon-container mb-3 flex-center">
                        <FileSpreadsheet size={48} className="text-primary-custom" />
                      </div>
                      <h4 className="fw-bold text-dark">Download Clinical Studies Data</h4>
                      <p className="text-muted mx-auto" style={{ maxWidth: '500px' }}>
                        Generate and download the complete patients registry dataset, including quiz scores, consent timelines, and attendance checklists in a standard CSV format.
                      </p>
                      
                      <button 
                        type="button" 
                        className="btn btn-primary btn-lg px-5 mt-3 flex-center gap-2 mx-auto bg-primary-custom"
                        style={{ borderRadius: '8px' }}
                        onClick={handleExport}
                        disabled={exporting}
                      >
                        <Download size={20} />
                        <span>{exporting ? 'Generating...' : 'Export Complete CSV Report'}</span>
                      </button>

                      {exportMessage && (
                        <p className="mt-3 font-semibold text-info-custom">{exportMessage}</p>
                      )}
                    </div>
                  )}
                </>
              )}

            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
