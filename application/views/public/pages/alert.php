<!-- Breadcromb Area Start -->
<section class="jobguru-breadcromb-area">
    
</section>
<!-- Breadcromb Area End -->


<!-- Alert Area Start -->
<section class="jobguru-login-area section_70">
    <div class="container">
        <?php if($this->session->flashdata('success')) { ?>
            <div class="alert alert-success text-center">
                <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
            </div><br>
        <?php } else if($this->session->flashdata('error')) { ?>
            <div class="alert alert-danger text-center">
                <strong>Warning!</strong> <?php echo $this->session->flashdata('error'); ?>
            </div><br>
        <?php } ?>        
        <br>
        <div class="dont_have">
            <a href="/" class="jobguru-btn">Return to home</a>
        </div>
    </div>
</section>
<!-- Alert Area End -->