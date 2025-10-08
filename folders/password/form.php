<!-- This file Only PHP Functions -->
<?php include 'function.php';?>
    <link rel="stylesheet" href="https://unpkg.com/dropzone/dist/dropzone.css" />
    <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
    <script src="https://unpkg.com/dropzone"></script>
    <script src="https://unpkg.com/cropperjs"></script>
<?php 
// Form variables
$btn_text              = "Save";
$btn_action            = "create";
$is_btn_disable        = "";
$btn_cancel            = "index.php";

$unique_id             = $_SESSION['user_id'];

$warning_msg           = "";
$old_readonly          = "";
$old_password          = "";
$new_password          = "";
$confirm_password       = "";
$is_active             = 1;

if (isset($_GET["default"])) {
    $old_readonly   = "readonly";
    $old_password   = "password";
    $warning_msg    = "Please Change Your Default password";
}

$staff_details       = staff_name($_SESSION['staff_id']);
$staf_name           = disname($staff_details[0]['staff_name']);
$staff_designation   = disname($staff_details[0]['designation_unique_id']);
$designation_details = designation($staff_designation);
$designation_type    = disname($designation_details[0]['designation']);

?>
  <style>

    .checklist {
      margin: 10px 0;
      list-style: none;
      padding-left: 0;
    }

    .checklist li {
      margin-bottom: 5px;
      color: red;
    }

    .checklist li.valid {
      color: green;
    }

    .strength-bar {
      height: 10px;
      width: 300px;
      background: #e0e0e0;
      border-radius: 5px;
      overflow: hidden;
      margin-top: 10px;
    }

    .strength-fill {
      height: 10px;
      width: 0%;
      background: red;
      transition: width 0.3s ease;
    }

    button {
      margin-top: 15px;
      padding: 10px 20px;
      font-size: 16px;
    }

    button:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    .hidden {
      display: none;
    }
  </style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated">
                <div class="row">                                    
                    <div class="col-12">
							<input type="hidden" id="unique_id" name="unique_id" class="form-control" value="<?php echo $unique_id; ?>" >
                            <?php if ($warning_msg) :?>
                            <h4 class="header-title text-danger"><?php echo $warning_msg; ?></h4>
                            <?php endif; ?>

                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="old_password"> Old Password </label>
                                <!-- <div class="col-md-4">
                                    <input type="text" id="old_password" name="old_password" class="form-control" value="<?php echo $old_password; ?>" <?php echo $old_readonly; ?>>
                                </div> -->
                                <div class="input-group input-group-merge col-md-4">
                                    <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Old Password" <?php echo $old_readonly; ?> value="<?php echo $old_password; ?>">
                                    <div class="input-group-append" data-password="false">
                                        <div class="input-group-text form-control">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                                <label class="col-md-6 col-form-label" for="staff_name" style="text-align: center; font-size: 20px"> <?php echo $staf_name; ?> </label>
                                
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="new_password"> New Password</label>
                                <!-- <div class="col-md-4">
                                    <input type="text" id="new_password" name="new_password" class="form-control" value="<?php echo $new_password; ?>" >
                                </div> -->
                                <div class="input-group input-group-merge col-md-4">
                                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="New Password"  onkeyup="validatePassword()" value="<?php echo $new_password; ?>">
                                    <div class="input-group-append" data-password="false">
                                        <div class="input-group-text form-control">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                                <label class="col-md-6 col-form-label" for="designation_name" style="text-align: center; font-size: 16px">( <?php echo $designation_type; ?>) </label>
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12">
                                <ul class="checklist hidden" id="checklist">
                                    <li id="length">✅ At least 8 characters</li>
                                    <li id="lower">✅ Contains lowercase letter</li>
                                    <li id="upper">✅ Contains uppercase letter</li>
                                    <li id="number">✅ Contains number</li>
                                    <li id="special">✅ Contains special character</li>
                                </ul>
                            
                                <div class="strength-bar hidden" id="strength-bar">
                                    <div id="strength-fill" class="strength-fill"></div>
                                </div>
                               </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="confirm_password"> Confirm Password </label>
                                <!-- <div class="col-md-4">
                                    <input type="text" id="confirm_password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" >
                                </div> -->
                                <div class="input-group input-group-merge col-md-4">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password" value="<?php echo $confirm_password; ?>">
                                    <div class="input-group-append" data-password="false">
                                        <div class="input-group-text form-control">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <label class="col-md-2 col-form-label" style="text-align: center;" for="profile_image"> </label>
                                <div class="col-md-4">
                                    
                                    <div class="image_area">
                                        <form method="post">
                                            <label for="upload_image">
                                                <img src="<?php echo $_SESSION['user_image']?>" id="uploaded_image" class="img-responsive img-circle" />
                                                 <input type="hidden" name="image_name"  id="image_name"  />
                                                    <div class="overlay">
                                                        <div class="text">Click to Change Profile Image</div>
                                                    </div>
                                                <input type="file" name="image" class="image" id="upload_image" style="display:none" />
                                            </label>
                                        </form>
                                    </div>

                                </div>
                                <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Crop Image Before Upload</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="img-container">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <img src="" id="sample_image" />
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="preview"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" id="crop" class="btn btn-primary">Crop</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                   
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                     <?php echo btn_cancel($btn_cancel);?>
                                </div>
                                
                            </div>
                    </div>
                </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  



<style>

    .image_area {
          position: relative;
        }

        img {
            display: block;
            max-width: 100%;
        }

        .preview {
            overflow: hidden;
            width: 160px; 
            height: 160px;
            margin: 10px;
            border: 1px solid red;
        }

        .modal-lg{
            max-width: 1000px !important;
        }

        .overlay {
          position: absolute;
          bottom: 10px;
          left: 0;
          right: 0;
          background-color: rgba(255, 255, 255, 0.5);
          overflow: hidden;
          height: 0;
          transition: .5s ease;
          width: 50%;
        }

        .image_area:hover .overlay {
          height: 60%;
          cursor: pointer;
        }

        .text {
          color: #333;
          font-size: 10px;
          position: absolute;
          top: 50%;
          left: 50%;
          -webkit-transform: translate(-50%, -50%);
          -ms-transform: translate(-50%, -50%);
          transform: translate(-50%, -50%);
          text-align: center;
        }

        .img-circle {
        border-radius: 50%;
        display: block;
        max-width: 50%;

        }
</style>

<script>

$(document).ready(function(){

    var $modal = $('#modal');

    var image = document.getElementById('sample_image');
	var unique_id = document.getElementById('unique_id').value;

    var cropper;

    $('#upload_image').change(function(event){
        var files = event.target.files;

        var done = function(url){  
            image.src = url;
            $modal.modal('show');
        };

        if(files && files.length > 0)
        {
            reader = new FileReader();
            reader.onload = function(event)
            {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });

    $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3,
            preview:'.preview'
        });
    }).on('hidden.bs.modal', function(){
        cropper.destroy();
        cropper = null;
    });

    $('#crop').click(function(){
        canvas = cropper.getCroppedCanvas({
            width:400,
            height:400
        });

        canvas.toBlob(function(blob){
            url = URL.createObjectURL(blob);
            var reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = function(){
                var base64data = reader.result;
                $.ajax({
                    url:'folders/password/upload.php',
                    method:'POST',
                    data:{
                    	image:base64data,
                    	unique_id:unique_id,
					},
                    success:function(data)
                    {   var split_data = data.split("/");
                      
                        $modal.modal('hide');
                        $('#uploaded_image').attr('src', data);
                        $("#image_name").val(split_data[3]);
                    }
                });
            };
        });
    });
    
});
</script>