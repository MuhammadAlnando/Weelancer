<form action="" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo $employer->username; ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $employer->email; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
