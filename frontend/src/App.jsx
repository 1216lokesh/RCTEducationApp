import React from 'react';
import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import { LanguageProvider } from './context/LanguageContext';

// Authentication Pages
import Login from './pages/auth/Login';
import Register from './pages/auth/Register';

// Patient Pages
import PatientDashboard from './pages/patient/Dashboard';
import Baseline from './pages/patient/Baseline';
import ProcedureInfo from './pages/patient/ProcedureInfo';
import Counselling from './pages/patient/Counselling';
import Consent from './pages/patient/Consent';
import Satisfaction from './pages/patient/Satisfaction';
import Education from './pages/patient/Education';
import Anxiety from './pages/patient/Anxiety';
import Quiz from './pages/patient/Quiz';
import PostOp from './pages/patient/PostOp';
import Followup1Week from './pages/patient/Followup1Week';

// Admin Pages
import AdminDashboard from './pages/admin/Dashboard';
import AdminPatients from './pages/admin/Patients';
import AdminPatientDetail from './pages/admin/PatientDetail';

// Route helper to check login status
function ProtectedRoute({ children }) {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="flex-center full-screen">
        <div className="loading-spinner"></div>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return children;
}

// Route helper to check roles
function RoleProtectedRoute({ children, allowedRole }) {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="flex-center full-screen">
        <div className="loading-spinner"></div>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (user.role !== allowedRole) {
    // Redirect patients to patient dashboard and admins to admin panel
    return user.role === 'admin'
      ? <Navigate to="/admin" replace />
      : <Navigate to="/patient/dashboard" replace />;
  }

  return children;
}

// Redirects home path / depending on session role
function RootRedirect() {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="flex-center full-screen">
        <div className="loading-spinner"></div>
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return user.role === 'admin'
    ? <Navigate to="/admin" replace />
    : <Navigate to="/patient/dashboard" replace />;
}

export default function App() {
  return (
    <LanguageProvider>
      <AuthProvider>
        <HashRouter>
          <Routes>
            {/* Public Auth Routes */}
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />

            {/* Root Redirection */}
            <Route path="/" element={<RootRedirect />} />

            {/* Patient Panel Routes */}
            <Route path="/patient/dashboard" element={
              <RoleProtectedRoute allowedRole="patient">
                <PatientDashboard />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/baseline/:aptId" element={
              <RoleProtectedRoute allowedRole="patient">
                <Baseline />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/procedure-info/:aptId" element={
              <RoleProtectedRoute allowedRole="patient">
                <ProcedureInfo />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/education/:aptId" element={
              <RoleProtectedRoute allowedRole="patient">
                <Education />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/anxiety/:aptId" element={
              <RoleProtectedRoute allowedRole="patient">
                <Anxiety />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/quiz/:aptId" element={
              <RoleProtectedRoute allowedRole="patient">
                <Quiz />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/counselling" element={
              <RoleProtectedRoute allowedRole="patient">
                <Counselling />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/consent" element={
              <RoleProtectedRoute allowedRole="patient">
                <Consent />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/satisfaction" element={
              <RoleProtectedRoute allowedRole="patient">
                <Satisfaction />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/postop" element={
              <RoleProtectedRoute allowedRole="patient">
                <PostOp />
              </RoleProtectedRoute>
            } />
            <Route path="/patient/followup-1week" element={
              <RoleProtectedRoute allowedRole="patient">
                <Followup1Week />
              </RoleProtectedRoute>
            } />

            {/* Admin Panel Routes */}
            <Route path="/admin" element={
              <RoleProtectedRoute allowedRole="admin">
                <AdminDashboard />
              </RoleProtectedRoute>
            } />
            <Route path="/admin/patients" element={
              <RoleProtectedRoute allowedRole="admin">
                <AdminPatients />
              </RoleProtectedRoute>
            } />
            <Route path="/admin/patient/:id" element={
              <RoleProtectedRoute allowedRole="admin">
                <AdminPatientDetail />
              </RoleProtectedRoute>
            } />

            {/* Fallback Catch-all Redirect */}
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </HashRouter>
      </AuthProvider>
    </LanguageProvider>
  );
}
