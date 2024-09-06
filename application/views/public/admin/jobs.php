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
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h4 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar .nav-link {
            color: white;
            font-size: 18px;
            margin: 10px 0;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
            border-radius: 5px;
            padding-left: 10px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
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
            <i class="fa fa-sign-out"></i> Logout
        </a>
    </div>

    <!-- Content Area -->
    <div class="content">
        <h1>Jobs</h1>
        <a href="<?php echo site_url('admin/jobs/add'); ?>" class="btn btn-primary mb-3"><i class="fa fa-plus"></i> Add Job</a>
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
                        <a href="<?php echo site_url('admin/jobs/edit/'.$job->id); ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        <a href="<?php echo site_url('admin/jobs/delete/'.$job->id); ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
