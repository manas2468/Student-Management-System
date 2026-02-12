<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user type
$stmt = $conn->prepare("SELECT user_type FROM user WHERE ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$is_admin = ($user['user_type'] === 'admin');
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>College Management System - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand i {
            font-size: 2rem;
            color: #f39c12;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            cursor: pointer;
            font-size: 1.3rem;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .notification-bell:hover {
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .notification-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            z-index: 2000;
            overflow: hidden;
        }

        .notification-dropdown.show {
            display: flex;
        }

        .notification-header {
            padding: 15px 20px;
            background: #3498db;
            color: white;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item.unread {
            background: #e8f4fd;
        }

        .notification-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .notification-message {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #999;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 80px;
            width: 280px;
            height: calc(100vh - 80px);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            z-index: 999;
            transition: transform 0.3s ease;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #f8f9fa;
            color: #3498db;
            border-left-color: #3498db;
        }

        .sidebar-menu a i {
            width: 20px;
            font-size: 1.2rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: calc(100vh - 80px);
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 10px;
        }

        .dashboard-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--card-color, #3498db) 0%, var(--card-color-light, #2980b9) 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .stat-card.primary { --card-color: #3498db; --card-color-light: #2980b9; }
        .stat-card.success { --card-color: #27ae60; --card-color-light: #2ecc71; }
        .stat-card.info { --card-color: #8e44ad; --card-color-light: #9b59b6; }
        .stat-card.warning { --card-color: #f39c12; --card-color-light: #e67e22; }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-light) 100%);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
            font-weight: 500;
        }

        /* Content Cards */
        .content-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.4rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: modalSlideIn 0.3s ease;
            max-height: 85vh;
            overflow-y: auto;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-right: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-danger {
            background: #e74535;
            color: white;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        /* Certificate Cards */
        .certificate-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .certificate-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .certificate-card:hover {
            border-color: #3498db;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .certificate-icon {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 10px;
        }

        .certificate-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .certificate-type {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
        }

        .certificate-date {
            font-size: 0.8rem;
            color: #999;
        }

        /* Activity Timeline */
        .activity-timeline {
            position: relative;
            padding-left: 40px;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .activity-item {
            position: relative;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 25px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #3498db;
            border: 3px solid white;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .activity-title {
            font-weight: 600;
            color: #333;
        }

        .activity-type-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .activity-type-event { background: #e8f4fd; color: #3498db; }
        .activity-type-announcement { background: #fef5e7; color: #f39c12; }
        .activity-type-exam { background: #fadbd8; color: #e74c3c; }
        .activity-type-workshop { background: #d5f4e6; color: #27ae60; }

        .activity-description {
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .activity-date {
            font-size: 0.85rem;
            color: #999;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                display: none;
            }
            
            .main-content {
                padding: 20px;
            }
            
            .dashboard-title {
                font-size: 2rem;
            }

            .notification-dropdown {
                width: 90vw;
                right: 5vw;
            }
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 3000;
            opacity: 0;
            transform: translateX(400px);
            transition: all 0.3s ease;
            min-width: 300px;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .toast.success { background: #27ae60; }
        .toast.error { background: #e74535; }
        .toast.info { background: #3498db; }
        .toast.warning { background: #f39c12; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="brand">
                <i class="fas fa-graduation-cap"></i>
                College Management System
            </div>
            <nav class="nav-links">
                <a href="#" class="active" data-page="dashboard"><i class="fas fa-home"></i> Dashboard</a>
                <a href="#" data-page="certificates"><i class="fas fa-certificate"></i> Certificates</a>
                <a href="#" data-page="activities"><i class="fas fa-calendar-alt"></i> Activities</a>
            </nav>
            <div class="user-profile">
                <div class="notification-bell" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                </div>
                <div class="user-avatar" id="userAvatar"><?php echo substr($username, 0, 2); ?></div>
                <div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($username); ?></div>
                    <div style="font-size: 0.8rem; opacity: 0.8;"><?php echo $is_admin ? 'Admin' : 'Student'; ?></div>
                </div>
                <button onclick="logout()" style="background: none; border: none; color: white; cursor: pointer;"><i class="fas fa-sign-out-alt"></i></button>
            </div>
        </div>
    </header>

    <!-- Notification Dropdown -->
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <span>Notifications</span>
            <span style="cursor: pointer; font-size: 0.9rem;" onclick="markAllAsRead()">Mark all as read</span>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications</p>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <a href="#" class="active" data-page="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="#" data-page="certificates">
                <i class="fas fa-certificate"></i>
                My Certificates
            </a>
            <a href="#" data-page="activities">
                <i class="fas fa-calendar-check"></i>
                Activities
            </a>
            <?php if($is_admin): ?>
            <a href="#" data-page="manage-certificates">
                <i class="fas fa-upload"></i>
                Manage Certificates
            </a>
            <a href="#" data-page="manage-activities">
                <i class="fas fa-tasks"></i>
                Manage Activities
            </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Dashboard View -->
        <div id="dashboardView">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p class="dashboard-subtitle">College Management System - Academic Year 2024-25</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card primary" onclick="navigateToPage('certificates')">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="totalCertificates">0</div>
                    <div class="stat-label">My Certificates</div>
                </div>

                <div class="stat-card success" onclick="navigateToPage('activities')">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="totalActivities">0</div>
                    <div class="stat-label">Active Activities</div>
                </div>

                <div class="stat-card info">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                    <div class="stat-value" id="unreadNotifications">0</div>
                    <div class="stat-label">Unread Notifications</div>
                </div>
            </div>
        </div>

        <!-- Certificates View -->
        <div id="certificatesView" style="display: none;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">My Certificates</h1>
                <p class="dashboard-subtitle">View and download your certificates</p>
            </div>

            <div class="content-card">
                <div class="certificate-grid" id="certificateGrid">
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <p>No certificates available</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activities View -->
        <div id="activitiesView" style="display: none;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">College Activities</h1>
                <p class="dashboard-subtitle">Stay updated with college events and announcements</p>
            </div>

            <div class="content-card">
                <div class="activity-timeline" id="activityTimeline">
                    <div class="empty-state">
                        <i class="fas fa-calendar-alt"></i>
                        <p>No activities available</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin: Manage Certificates View -->
        <?php if($is_admin): ?>
        <div id="manageCertificatesView" style="display: none;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Manage Certificates</h1>
                <p class="dashboard-subtitle">Upload and manage student certificates</p>
            </div>

            <div class="content-card">
                <button class="btn btn-primary" onclick="showModal('uploadCertificateModal')">
                    <i class="fas fa-upload"></i> Upload Certificate
                </button>

                <div class="table-container" style="margin-top: 20px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Certificate Name</th>
                                <th>Type</th>
                                <th>Upload Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="allCertificatesTable">
                            <tr>
                                <td colspan="5" style="text-align: center;">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Admin: Manage Activities View -->
        <div id="manageActivitiesView" style="display: none;">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Manage Activities</h1>
                <p class="dashboard-subtitle">Create and manage college activities</p>
            </div>

            <div class="content-card">
                <button class="btn btn-primary" onclick="showModal('createActivityModal')">
                    <i class="fas fa-plus"></i> Create Activity
                </button>

                <div class="activity-timeline" id="adminActivityTimeline" style="margin-top: 20px;">
                    <div class="empty-state">
                        <i class="fas fa-calendar-alt"></i>
                        <p>No activities created yet</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Modals -->
    <?php if($is_admin): ?>
    <!-- Upload Certificate Modal -->
    <div id="uploadCertificateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('uploadCertificateModal')">&times;</span>
            <h2>Upload Certificate</h2>
            <form id="uploadCertificateForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Select Student</label>
                    <select id="certStudentId" required>
                        <option value="">Select a student...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Certificate Name</label>
                    <input type="text" id="certName" required placeholder="e.g., Course Completion Certificate">
                </div>
                <div class="form-group">
                    <label>Certificate Type</label>
                    <select id="certType" required>
                        <option value="">Select type...</option>
                        <option value="Course Completion">Course Completion</option>
                        <option value="Achievement">Achievement</option>
                        <option value="Participation">Participation</option>
                        <option value="Excellence">Excellence</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="certDescription" rows="3" placeholder="Optional description"></textarea>
                </div>
                <div class="form-group">
                    <label>Certificate File (PDF, JPG, PNG)</label>
                    <input type="file" id="certFile" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload Certificate</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('uploadCertificateModal')">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Create Activity Modal -->
    <div id="createActivityModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createActivityModal')">&times;</span>
            <h2>Create Activity</h2>
            <form id="createActivityForm">
                <div class="form-group">
                    <label>Activity Title</label>
                    <input type="text" id="activityTitle" required placeholder="e.g., Tech Fest 2025">
                </div>
                <div class="form-group">
                    <label>Activity Type</label>
                    <select id="activityType" required>
                        <option value="">Select type...</option>
                        <option value="event">Event</option>
                        <option value="announcement">Announcement</option>
                        <option value="exam">Exam</option>
                        <option value="workshop">Workshop</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="activityDescription" rows="4" required placeholder="Describe the activity..."></textarea>
                </div>
                <div class="form-group">
                    <label>Event Date (Optional)</label>
                    <input type="date" id="activityDate">
                </div>
                <button type="submit" class="btn btn-primary">Create Activity</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('createActivityModal')">Cancel</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
        const userId = <?php echo $user_id; ?>;

        // Initialize app
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            loadNotificationCount();
            loadDashboardData();
            
            // Poll for new notifications every 30 seconds
            setInterval(loadNotificationCount, 30000);
        });

        function initializeApp() {
            // Setup navigation
            document.querySelectorAll('[data-page]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    navigateToPage(this.dataset.page);
                });
            });

            // Close modals on outside click
            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            });

            // Close notification dropdown on outside click
            document.addEventListener('click', function(e) {
                const dropdown = document.getElementById('notificationDropdown');
                const bell = document.querySelector('.notification-bell');
                if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });

            // Setup form handlers
            setupFormHandlers();
        }

        function setupFormHandlers() {
            <?php if($is_admin): ?>
            // Upload Certificate Form
            document.getElementById('uploadCertificateForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('action', 'upload');
                formData.append('student_id', document.getElementById('certStudentId').value);
                formData.append('certificate_name', document.getElementById('certName').value);
                formData.append('certificate_type', document.getElementById('certType').value);
                formData.append('description', document.getElementById('certDescription').value);
                formData.append('certificate_file', document.getElementById('certFile').files[0]);

                try {
                    const response = await fetch('api_certificates.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast('Certificate uploaded successfully!', 'success');
                        closeModal('uploadCertificateModal');
                        this.reset();
                        if (document.getElementById('manageCertificatesView').style.display !== 'none') {
                            loadAllCertificates();
                        }
                    } else {
                        showToast(data.message || 'Failed to upload certificate', 'error');
                    }
                } catch (error) {
                    showToast('Error uploading certificate', 'error');
                    console.error(error);
                }
            });

            // Create Activity Form
            document.getElementById('createActivityForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('action', 'create');
                formData.append('title', document.getElementById('activityTitle').value);
                formData.append('description', document.getElementById('activityDescription').value);
                formData.append('activity_type', document.getElementById('activityType').value);
                formData.append('event_date', document.getElementById('activityDate').value);

                try {
                    const response = await fetch('api_activities.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        showToast('Activity created and notifications sent!', 'success');
                        closeModal('createActivityModal');
                        this.reset();
                        if (document.getElementById('manageActivitiesView').style.display !== 'none') {
                            loadActivitiesForAdmin();
                        }
                        loadActivities();
                    } else {
                        showToast(data.message || 'Failed to create activity', 'error');
                    }
                } catch (error) {
                    showToast('Error creating activity', 'error');
                    console.error(error);
                }
            });

            // Load students for certificate upload
            loadStudentsList();
            <?php endif; ?>
        }

        async function loadNotificationCount() {
            try {
                const response = await fetch('api_notifications.php?action=get_unread_count');
                const data = await response.json();
                
                if (data.success) {
                    const badge = document.getElementById('notificationBadge');
                    const count = data.count;
                    
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                    
                    document.getElementById('unreadNotifications').textContent = count;
                }
            } catch (error) {
                console.error('Error loading notification count:', error);
            }
        }

        async function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            
            if (dropdown.classList.contains('show')) {
                await loadNotifications();
            }
        }

        async function loadNotifications() {
            try {
                const response = await fetch('api_notifications.php?action=get_all&limit=20');
                const data = await response.json();
                
                if (data.success) {
                    const list = document.getElementById('notificationList');
                    
                    if (data.notifications.length === 0) {
                        list.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-bell-slash"></i>
                                <p>No notifications</p>
                            </div>
                        `;
                    } else {
                        list.innerHTML = data.notifications.map(notif => `
                            <div class="notification-item ${notif.is_read == 0 ? 'unread' : ''}" onclick="markAsRead(${notif.id})">
                                <div class="notification-title">${notif.title}</div>
                                <div class="notification-message">${notif.message}</div>
                                <div class="notification-time">${formatTimeAgo(notif.created_at)}</div>
                            </div>
                        `).join('');
                    }
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }

        async function markAsRead(notifId) {
            try {
                const formData = new FormData();
                formData.append('action', 'mark_read');
                formData.append('notification_id', notifId);
                
                await fetch('api_notifications.php', {
                    method: 'POST',
                    body: formData
                });
                
                loadNotificationCount();
                loadNotifications();
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }

        async function markAllAsRead() {
            try {
                const formData = new FormData();
                formData.append('action', 'mark_all_read');
                
                await fetch('api_notifications.php', {
                    method: 'POST',
                    body: formData
                });
                
                loadNotificationCount();
                loadNotifications();
                showToast('All notifications marked as read', 'success');
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        }

        async function loadDashboardData() {
            loadCertificateCount();
            loadActivityCount();
        }

        async function loadCertificateCount() {
            try {
                const response = await fetch('api_certificates.php?action=get_certificates');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('totalCertificates').textContent = data.certificates.length;
                }
            } catch (error) {
                console.error('Error loading certificate count:', error);
            }
        }

        async function loadActivityCount() {
            try {
                const response = await fetch('api_activities.php?action=get_all');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('totalActivities').textContent = data.activities.length;
                }
            } catch (error) {
                console.error('Error loading activity count:', error);
            }
        }

        async function loadCertificates() {
            try {
                const response = await fetch('api_certificates.php?action=get_certificates');
                const data = await response.json();
                
                if (data.success) {
                    const grid = document.getElementById('certificateGrid');
                    
                    if (data.certificates.length === 0) {
                        grid.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-certificate"></i>
                                <p>No certificates available</p>
                            </div>
                        `;
                    } else {
                        grid.innerHTML = data.certificates.map(cert => `
                            <div class="certificate-card" onclick="viewCertificate('${cert.file_path}')">
                                <div class="certificate-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="certificate-name">${cert.certificate_name}</div>
                                <div class="certificate-type">${cert.certificate_type}</div>
                                <div class="certificate-date">Uploaded: ${new Date(cert.upload_date).toLocaleDateString()}</div>
                            </div>
                        `).join('');
                    }
                }
            } catch (error) {
                console.error('Error loading certificates:', error);
            }
        }

        async function loadActivities() {
            try {
                const response = await fetch('api_activities.php?action=get_all');
                const data = await response.json();
                
                if (data.success) {
                    const timeline = document.getElementById('activityTimeline');
                    
                    if (data.activities.length === 0) {
                        timeline.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-calendar-alt"></i>
                                <p>No activities available</p>
                            </div>
                        `;
                    } else {
                        timeline.innerHTML = data.activities.map(activity => `
                            <div class="activity-item">
                                <div class="activity-header">
                                    <div class="activity-title">${activity.title}</div>
                                    <div class="activity-type-badge activity-type-${activity.activity_type}">${activity.activity_type}</div>
                                </div>
                                <div class="activity-description">${activity.description}</div>
                                <div class="activity-date">
                                    ${activity.event_date ? '<i class="fas fa-calendar"></i> ' + new Date(activity.event_date).toLocaleDateString() + ' | ' : ''}
                                    Posted ${formatTimeAgo(activity.created_at)}
                                </div>
                            </div>
                        `).join('');
                    }
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        }

        <?php if($is_admin): ?>
        async function loadStudentsList() {
            try {
                const response = await fetch('get_students.php');
                const data = await response.json();
                
                if (data.success) {
                    const select = document.getElementById('certStudentId');
                    select.innerHTML = '<option value="">Select a student...</option>' + 
                        data.students.map(student => 
                            `<option value="${student.ID}">${student.Username} (${student.Email})</option>`
                        ).join('');
                }
            } catch (error) {
                console.error('Error loading students:', error);
            }
        }

        async function loadAllCertificates() {
            try {
                const response = await fetch('api_certificates.php?action=get_certificates');
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.getElementById('allCertificatesTable');
                    
                    if (data.certificates.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No certificates uploaded yet</td></tr>';
                    } else {
                        tbody.innerHTML = data.certificates.map(cert => `
                            <tr>
                                <td>${cert.student_name}</td>
                                <td>${cert.certificate_name}</td>
                                <td>${cert.certificate_type}</td>
                                <td>${new Date(cert.upload_date).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn btn-primary btn-small" onclick="viewCertificate('${cert.file_path}')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-danger btn-small" onclick="deleteCertificate(${cert.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
            } catch (error) {
                console.error('Error loading certificates:', error);
            }
        }

        async function loadActivitiesForAdmin() {
            try {
                const response = await fetch('api_activities.php?action=get_all');
                const data = await response.json();
                
                if (data.success) {
                    const timeline = document.getElementById('adminActivityTimeline');
                    
                    if (data.activities.length === 0) {
                        timeline.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-calendar-alt"></i>
                                <p>No activities created yet</p>
                            </div>
                        `;
                    } else {
                        timeline.innerHTML = data.activities.map(activity => `
                            <div class="activity-item">
                                <div class="activity-header">
                                    <div class="activity-title">${activity.title}</div>
                                    <div>
                                        <span class="activity-type-badge activity-type-${activity.activity_type}">${activity.activity_type}</span>
                                    </div>
                                </div>
                                <div class="activity-description">${activity.description}</div>
                                <div class="activity-date">
                                    ${activity.event_date ? '<i class="fas fa-calendar"></i> ' + new Date(activity.event_date).toLocaleDateString() + ' | ' : ''}
                                    Posted ${formatTimeAgo(activity.created_at)}
                                </div>
                                <div style="margin-top: 10px;">
                                    <button class="btn btn-danger btn-small" onclick="deleteActivity(${activity.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            }
        }

        async function deleteCertificate(certId) {
            if (!confirm('Are you sure you want to delete this certificate?')) return;
            
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('certificate_id', certId);
                
                const response = await fetch('api_certificates.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Certificate deleted successfully', 'success');
                    loadAllCertificates();
                } else {
                    showToast(data.message || 'Failed to delete certificate', 'error');
                }
            } catch (error) {
                showToast('Error deleting certificate', 'error');
                console.error(error);
            }
        }

        async function deleteActivity(activityId) {
            if (!confirm('Are you sure you want to delete this activity?')) return;
            
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('activity_id', activityId);
                
                const response = await fetch('api_activities.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('Activity deleted successfully', 'success');
                    loadActivitiesForAdmin();
                    loadActivities();
                } else {
                    showToast(data.message || 'Failed to delete activity', 'error');
                }
            } catch (error) {
                showToast('Error deleting activity', 'error');
                console.error(error);
            }
        }
        <?php endif; ?>

        function viewCertificate(filePath) {
            window.open(filePath, '_blank');
        }

        function navigateToPage(page) {
            // Hide all views
            document.querySelectorAll('#mainContent > div').forEach(view => {
                view.style.display = 'none';
            });
            
            // Update active navigation
            document.querySelectorAll('[data-page]').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelectorAll(`[data-page="${page}"]`).forEach(link => {
                link.classList.add('active');
            });
            
            // Show selected view and load data
            switch(page) {
                case 'dashboard':
                    document.getElementById('dashboardView').style.display = 'block';
                    loadDashboardData();
                    break;
                case 'certificates':
                    document.getElementById('certificatesView').style.display = 'block';
                    loadCertificates();
                    break;
                case 'activities':
                    document.getElementById('activitiesView').style.display = 'block';
                    loadActivities();
                    break;
                <?php if($is_admin): ?>
                case 'manage-certificates':
                    document.getElementById('manageCertificatesView').style.display = 'block';
                    loadAllCertificates();
                    break;
                case 'manage-activities':
                    document.getElementById('manageActivitiesView').style.display = 'block';
                    loadActivitiesForAdmin();
                    break;
                <?php endif; ?>
            }
        }

        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}`;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }

        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return 'Just now';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
            return date.toLocaleDateString();
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
                document.getElementById('notificationDropdown').classList.remove('show');
            }
        });
    </script>
</body>
</html>