<!-- Breadcromb Area Start -->
<section class="jobguru-breadcromb-area">
    <div class="breadcromb-bottom">
        
    </div>
</section>
<!-- Breadcromb Area End -->

<!-- Login Area Start -->
<section class="jobguru-login-area section_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="login-box">
                <?php if($this->session->flashdata('success')): ?>
                    <!-- Error message -->
                    <div class="alert alert-success alert-dismissible fade show text-center mb-4" role="alert">
                        <strong><?php echo $this->session->flashdata('success'); ?></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php elseif($this->session->flashdata('error')): ?>
                    <!-- Error message -->
                    <div class="alert alert-warning alert-dismissible fade show text-center mb-4" role="alert">
                        <strong><?php echo $this->session->flashdata('error'); ?></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                    <div class="login-title">
                        <h3>Admin Panel</h3>
                    </div>

                    <?php echo form_open('admin/login', 'id="form"') ?>
                        <div class="single-login-field">
                           <input  name="email" type="email" placeholder="Email Address" value="<?php echo set_value('email'); ?>" required>
                           <?php echo form_error('email', '<small class="text-danger mx-1">', '</small>'); ?>
                        </div>

                        <div class="single-login-field">
                           <input name="password" type="password" placeholder="Password" required>
                           <?php echo form_error('password', '<small class="text-danger mx-1">', '</small>'); ?>
                        </div>
                        
                        <div class="single-login-field">
                           <button id="form-btn" type="submit" style="background-color: #A52A2A;">&nbsp;Login&nbsp;<i class='fa fa-spinner fa-spin' style="display:none;"></i></button>
                        </div>
                     </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Login Area End -->