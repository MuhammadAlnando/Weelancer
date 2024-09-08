<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Jobs</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/img/favicon/favicon-32x32.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">
    <!-- Custom CSS -->
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

        .table {
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #ffffff;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .table .btn {
            font-size: 0.875rem;
            border-radius: 0.25rem;
        }

        .table .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .table .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
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
        <h1>Jobs</h1>
        
        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <!-- Job Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?php echo $job->id; ?></td>
                        <td><?php echo $job->title; ?></td>
                        <td><?php echo $job->description; ?></td>
                        <td>
                            <a href="<?php echo site_url('admin/delete_job/'.$job->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job?');">
                               <i class="fa fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
