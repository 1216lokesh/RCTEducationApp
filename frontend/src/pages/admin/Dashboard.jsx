import React, { useEffect, useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useLanguage } from '../../context/LanguageContext';
import { 
  Users, 
  FileCheck, 
  ClipboardList, 
  Download, 
  Activity, 
  UserPlus, 
  LogOut, 
  ChevronRight, 
  TrendingUp, 
  BookOpen, 
  ShieldCheck 
} from 'lucide-react';
import api from '../../services/api';
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  Tooltip, 
  ResponsiveContainer, 
  Cell, 
  PieChart, 
  Pie 
} from 'recharts';

export default function AdminDashboard() {
  const { user, logout } = useAuth();
  const { t } = useLanguage();
  const navigate = useNavigate();

  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await api.get('/admin/get_stats.php');
        if (response.data.success) {
          setStats(response.data);
        } else {
          setError(response.data.message || 'Failed to fetch admin statistics.');
        }
      } catch (err) {
        console.error('Error fetching admin stats:', err);
        setError('A network error occurred while loading stats.');
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  if (loading) {
    return (
      <div className="flex-center full-screen">
        <div className="loading-spinner"></div>
        <p className="mt-2 text-muted">{t('loading') || 'Loading statistics...'}</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex-center full-screen text-center">
        <div className="card shadow-sm p-4 text-danger" style={{ maxWidth: '400px' }}>
          <h4>Error</h4>
          <p>{error}</p>
          <button className="btn btn-primary mt-2" onClick={() => window.location.reload()}>Retry</button>
        </div>
      </div>
    );
  }

  const { totalPatients, totalAssigned, consentCount, completedSteps, totalExpectedSteps, recentPatients } = stats;

  // Pie chart data: Assigned vs Unassigned
  const assignmentData = [
    { name: 'Assigned', value: totalAssigned, color: '#10b981' },
    { name: 'Unassigned', value: totalPatients - totalAssigned, color: '#64748b' }
  ];

  // Bar chart data: Journey Progress
  const progressData = [
    { name: 'Completed Steps', value: completedSteps, color: '#6366f1' },
    { name: 'Remaining Steps', value: Math.max(0, totalExpectedSteps - completedSteps), color: '#e2e8f0' }
  ];

  return (
    <div className="admin-container">
      {/* Sidebar / Left Navigation */}
      <aside className="admin-sidebar">
        <div className="sidebar-brand">
          <Activity size={24} className="text-primary" />
          <span>RCT Admin Panel</span>
        </div>
        
        <nav className="sidebar-nav">
          <Link to="/admin" className="nav-item active">
            <TrendingUp size={18} />
            <span>Dashboard</span>
          </Link>
          <Link to="/admin/patients" className="nav-item">
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
            <h6>{user?.name || 'Administrator'}</h6>
            <span>Admin Portal</span>
          </div>
        </div>
      </aside>

      {/* Main Page Area */}
      <main className="admin-main-content">
        {/* Welcome Header */}
        <header className="admin-header">
          <div>
            <h1 className="h2 fw-bold text-dark">Dashboard Overview</h1>
            <p className="text-muted mb-0">Track clinical progress and manage patient allocations.</p>
          </div>
          
          <div className="admin-quick-actions">
            <Link to="/admin/patients" className="btn btn-outline-primary btn-sm flex-center gap-1">
              <UserPlus size={16} />
              <span>View All Patients</span>
            </Link>
          </div>
        </header>

        {/* Stats Grid */}
        <section className="stats-cards-grid">
          {/* Card 1 */}
          <div className="stat-card shadow-sm border-0 bg-gradient-blue text-white">
            <div className="stat-card-body">
              <div className="stat-icon">
                <Users size={28} />
              </div>
              <div className="stat-info">
                <h6>Total Patients</h6>
                <h3>{totalPatients}</h3>
              </div>
            </div>
          </div>

          {/* Card 2 */}
          <div className="stat-card shadow-sm border-0 bg-gradient-green text-white">
            <div className="stat-card-body">
              <div className="stat-icon">
                <BookOpen size={28} />
              </div>
              <div className="stat-info">
                <h6>Procedures Assigned</h6>
                <h3>{totalAssigned}</h3>
              </div>
            </div>
          </div>

          {/* Card 3 */}
          <div className="stat-card shadow-sm border-0 bg-gradient-purple text-white">
            <div className="stat-card-body">
              <div className="stat-icon">
                <ShieldCheck size={28} />
              </div>
              <div className="stat-info">
                <h6>Consent Signed</h6>
                <h3>{consentCount}</h3>
              </div>
            </div>
          </div>

          {/* Card 4 */}
          <div className="stat-card shadow-sm border-0 bg-gradient-orange text-white">
            <div className="stat-card-body">
              <div className="stat-icon">
                <ClipboardList size={28} />
              </div>
              <div className="stat-info">
                <h6>Step Completions</h6>
                <h3>{completedSteps} <small style={{ fontSize: '0.9rem', opacity: 0.8 }}>/ {totalExpectedSteps}</small></h3>
              </div>
            </div>
          </div>
        </section>

        {/* Charts & Analytics Section */}
        <section className="dashboard-charts-row mt-4">
          {/* Chart 1: Procedures */}
          <div className="chart-card card shadow-sm border-0">
            <div className="card-header bg-transparent border-0 pt-4 px-4 pb-0">
              <h5 className="fw-bold mb-1">Procedures Allocation</h5>
              <p className="text-muted small mb-0">Assigned vs unassigned clinical procedures</p>
            </div>
            <div className="card-body p-4" style={{ height: '240px', position: 'relative' }}>
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie
                    data={assignmentData}
                    cx="50%"
                    cy="50%"
                    innerRadius={50}
                    outerRadius={75}
                    paddingAngle={5}
                    dataKey="value"
                  >
                    {assignmentData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Tooltip formatter={(value) => [`${value} Patients`]} />
                </PieChart>
              </ResponsiveContainer>
              <div className="chart-legends d-flex justify-content-center gap-4 mt-2">
                <span className="small text-muted flex-center gap-1">
                  <span className="legend-indicator bg-green"></span> Assigned ({totalAssigned})
                </span>
                <span className="small text-muted flex-center gap-1">
                  <span className="legend-indicator bg-slate"></span> Unassigned ({totalPatients - totalAssigned})
                </span>
              </div>
            </div>
          </div>

          {/* Chart 2: Step Completions */}
          <div className="chart-card card shadow-sm border-0">
            <div className="card-header bg-transparent border-0 pt-4 px-4 pb-0">
              <h5 className="fw-bold mb-1">Total Journey Completions</h5>
              <p className="text-muted small mb-0">Total checkpoints completed by all patients</p>
            </div>
            <div className="card-body p-4" style={{ height: '240px' }}>
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={progressData} layout="vertical" barSize={20}>
                  <XAxis type="number" hide />
                  <YAxis type="category" dataKey="name" width={110} stroke="#64748b" style={{ fontSize: '0.8rem' }} />
                  <Tooltip />
                  <Bar dataKey="value" radius={[0, 4, 4, 0]}>
                    {progressData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Bar>
                </BarChart>
              </ResponsiveContainer>
            </div>
          </div>
        </section>

        {/* Recent Patients Table */}
        <section className="dashboard-table-section mt-4">
          <div className="card shadow-sm border-0">
            <div className="card-header text-white p-3 flex-between bg-primary-custom">
              <h5 className="mb-0 fw-bold flex-center gap-2">
                <Users size={18} />
                <span>Recent Patients Registration</span>
              </h5>
              <Link to="/admin/patients" className="btn btn-light btn-sm font-semibold" style={{ fontSize: '0.8rem', borderRadius: '4px' }}>
                View All Patients
              </Link>
            </div>
            <div className="card-body p-0">
              {recentPatients && recentPatients.length > 0 ? (
                <div className="table-responsive">
                  <table className="table table-hover align-middle mb-0">
                    <thead className="table-light">
                      <tr>
                        <th className="ps-4" style={{ width: '60px' }}>#</th>
                        <th>Patient Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Registered Date</th>
                        <th className="pe-4 text-end">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      {recentPatients.map((patient, index) => (
                        <tr key={patient.id}>
                          <td className="ps-4 text-muted fw-semibold">{index + 1}</td>
                          <td className="fw-semibold text-dark">{patient.name}</td>
                          <td>{patient.email}</td>
                          <td>{patient.phone || 'No Phone'}</td>
                          <td>{new Date(patient.created_at).toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                          <td className="pe-4 text-end">
                            <Link 
                              to={`/admin/patient/${patient.id}`} 
                              className="btn btn-sm text-white px-3 flex-center gap-1 float-end bg-primary-custom"
                              style={{ borderRadius: '6px', width: 'fit-content' }}
                            >
                              <span>View Detail</span>
                              <ChevronRight size={14} />
                            </Link>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="alert alert-info m-4">No patients registered yet.</div>
              )}
            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
