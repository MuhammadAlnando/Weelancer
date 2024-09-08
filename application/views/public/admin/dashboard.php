<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/img/favicon/favicon-32x32.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">
    <!-- Custom CSS for Sidebar and Dashboard -->
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h4 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.5rem;
        }

        .sidebar .nav-link {
            color: white;
            font-size: 18px;
            margin: 10px 0;
            transition: background-color 0.3s, padding-left 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
            border-radius: 5px;
            padding-left: 15px;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .logout-link {
            color: white;
            font-size: 18px;
            text-align: center;
            margin-top: auto;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .content h1 {
            margin-bottom: 20px;
            font-size: 2rem;
            color: #343a40;
        }

        .alert {
            border-radius: 0.25rem;
            margin-bottom: 20px;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body .card-title {
            font-size: 1.25rem;
            margin-bottom: 0;
        }

        .card-body .card-text {
            font-size: 0.875rem;
        }

        .card-body .btn {
            font-size: 1rem;
            border-radius: 0.25rem;
            background-color: white;
            color: #007bff;
            border: 1px solid #007bff;
            transition: background-color 0.3s, color 0.3s;
        }

        .card-body .btn:hover {
            background-color: #007bff;
            color: white;
        }

        /* Background Colors */
        .bg-primary {
            background-color: #007bff !important;
            color: white;
        }

        .bg-success {
            background-color: #28a745 !important;
            color: white;
        }

        .bg-info {
            background-color: #17a2b8 !important;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                box-shadow: none;
            }
            .content {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-white">Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('admin/employers'); ?>"><i class="fa fa-building"></i> Employers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('admin/users'); ?>"><i class="fa fa-users"></i> Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('admin/jobs'); ?>"><i class="fa fa-briefcase"></i> Jobs</a>
            </li>
          

        </ul>
        <a href="<?php echo site_url('admin/logout'); ?>" class="logout-link">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Content Area -->
    <div class="content">
        <!-- Dashboard Content -->
        <h1>Dashboard</h1>
        <p>Welcome to the Admin Panel Dashboard. Here you can manage Employers, Users, Jobs, and Companies.</p>
        <div class="row">
            <!-- Use the .col-md-12 class to make each card occupy full width -->
            <div class="col-md-12 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title"><?php echo $employers_count; ?> Employers</h5>
                            <p class="card-text">Manage all the employers in the system.</p>
                        </div>
                        <a href="<?php echo site_url('admin/employers'); ?>" class="btn">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title"><?php echo $users_count; ?> Users</h5>
                            <p class="card-text">Manage the users of the platform.</p>
                        </div>
                        <a href="<?php echo site_url('admin/users'); ?>" class="btn">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title"><?php echo $jobs_count; ?> Jobs</h5>
                            <p class="card-text">Manage job listings and postings.</p>
                        </div>
                        <a href="<?php echo site_url('admin/jobs'); ?>" class="btn">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
