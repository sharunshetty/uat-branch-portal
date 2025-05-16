<?php

/**
 * @copyright   : (c) 2021 Copyright by LCode Technologies
 * @developer   : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app-core/app_auto_load.php');

/** Check User Session */
require_once(dirname(__FILE__) . '/check-login.php');

/** Current Page */
$page_pgm_code = "";

$page_title = "Customer Verification";
$page_link = "";

$parent_page_title = "";
$parent_page_link = "";

/** Table Settings */
$page_table_name = "ASSREQ_MASTER";
$primary_key = "ASSREQ_REF_NUM";

$errorMsg = "";

if(!isset($_GET['ref_Num']) || $_GET['ref_Num'] == "") {
    $errorMsg = "Invalid Request";
}else { 
    
    //Decode Request Data  
    $encrypt_ref_num = $main_app->strsafe_input($_GET['ref_Num']);
    $assref_num = $safe->str_decrypt($encrypt_ref_num, $_SESSION['SAFE_KEY']);
    if(!isset($assref_num) || $assref_num== false || $assref_num == "") {
        $errorMsg = "Invalid URL Request";
    } 
    $assref_num = $main_app->strsafe_output($assref_num);
    $enc_assref_num = $safe->str_encrypt( $assref_num, $_SESSION['SAFE_KEY']);
}

/** Page Header */
require( dirname(__FILE__) . '/../theme/app-header.php' );
?>

<?php 
    if(isset($errorMsg) && $errorMsg == "") {
        echo "<div class='abp-heading text-muted'>Account Ref No: <span class='text-danger'>$assref_num</span></div>";
   }
?>

<!-- Content : Start -->

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <?php if(isset($errorMsg) && $errorMsg != "") { ?>
                <div class="col-md-12 text-danger text-center mt-5 pt-5 h5"><?php echo $main_app->strsafe_output($ErrorMsg); ?></div>
            <?php } else { ?>
                <div class="col-md-12 form-group">
                    <div class="card card-outline card-brand">
                        <div class="card-body min-high2">
                            <div class="row justify-content-center">
                                <form id="app-service-cam" name="app-service-cam" class="form-material">
                                    <input type="hidden" name="WEBCAM_IMAGE" id="WEBCAM_IMAGE" value=""/>
                                    <!-- <input type="hidden" name="CUST_REF_NUM" id="CUST_REF_NUM" value=""/> -->
                                    <input type="hidden" id="asnVal" value="<?php echo $main_app->strsafe_input($enc_assref_num);?>"/>
                                    <!-- <input type="hidden" name="predata" id="predata" value=" echo $safe->str_encrypt(json_encode($output), $_SESSION['SAFE_KEY']); ?>"/> -->
                                    <input type="hidden" name="token" value="<?php echo (isset($_SESSION['APP_TOKEN'])) ? $_SESSION['APP_TOKEN'] : ""; ?>"/>

                                    <div class="row justify-content-center">

                                        <div class="col-md-6 col-lg-7 my-2">

                                        <div class="row mt-3">
                                            <div class="col-md-12 text-center h5">Take Customer Photo</div>
                                        </div>

                                        </div>

                                    </div>  

                                    <div class="row justify-content-center">
                                        <div class="col-md-9 col-lg-9">
                                            <div class="row">
                                                <div id="camError" style="display:none;" class="col-md-12 text-danger text-center py-3">We are unable to find the camera, please use camera enable device to complete the process.</div>

                                                <canvas id="canvas" width="300" height="250" class="bg-light" style="display:none;"></canvas>
                                                <div class="col-12 col-md-12 form-group justify-content-center text-center mb-0" id="my_camera"></div>
                                            
                                                <div class="col-12 col-md-12 form-group justify-content-center text-center mb-0" id="results"></div>

                                                <div class="col-md-12 col-md-12 justify-content-center text-center ml-2">
                                                    <button type="button" id="SWITCH_CAM" onclick="SwitchCam();" class="btn btn-outline-info btn-sm border border-info p-2 ml-0" style="display:none;"><i class="mdi mdi-camera-switch"></i> Switch Camera</button>
                                                    <button type="button" id="CAPTURE_PHOTO" style="margin:15px;" onclick="capturePhoto();" class="btn btn-primary p-2 ml-0"><i class="mdi mdi-camera"></i> Capture Photo</button>
                                                    <button type="button" id="tryPhoto" style="margin:15px;display:none;" onclick="try_Photo();" class="btn btn-warning ml-0"><i class="mdi mdi-camera"></i> Try Again</button>
                                                    <div class="small text-muted" id="try_caption" style="display:none;">If photo not clear try again otherwise press next button</div>
                                                    <div class="small text-danger mt-2"><mand>*</mand>Please click photo with white/clear background</div>                 
                                                </div>                                                   
                                            </div>
                                        </div> 
                                    </div>    
                                    
                                    
                                    <div class="row mt-4">                    
                                        <div class="col-md-12 mt-3 text-center">
                                            <?php echo '<button type="button" class="btn btn-secondary px-3 border"  name="prevBtn" id="prevBtn" onclick=gobackbut("'.$main_app->strsafe_input($enc_assref_num).'");> Go Back</button>';?>                              
                                           <button type="button" class="btn btn-primary px-4 mr-2 border" name="nextBtn" id="nextBtn" style="display:none;" onclick="upload_livePhoto('WEBCAM_IMAGE','PHOTO');">Next <i class="mdi mdi-arrow-right"></i></button>    
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            <?php }  ?>
        </div>
    </div>
</section>

<!-- Content : End -->

<?php 
/** Page Footer */
require( dirname(__FILE__) . '/../theme/app-footer.php' );
?>

<script type="text/javascript" src="<?php echo CDN_URL; ?>/theme/js/webcam.js?v=<?php echo CDN_VER; ?>"></script>


<script type="text/javascript">

    // $('#tryPhoto').click(function(){
    //     setup(); 
    // });   

    function gobackbut(ass_ref_num) {
        goto_url('ass-pancard-camera?ref_Num='+ass_ref_num);
    }


    function try_Photo(){
        $('#WEBCAM_IMAGE').val('');
        setup(); 
        show('my_camera');
        hide('results');
        show('CAPTURE_PHOTO');
        hide('tryPhoto');
        hide('try_caption');
        hide('nextBtn');
        get_camera_count();

    }

    Webcam.set({
        width: 350,
        height: 350,
        dest_width: 350,
        dest_height: 350,
        image_format: 'jpeg',
        jpeg_quality: 100,
        constraints: {
            facingMode: "environment"
        }
    });

    function setup() {
        Webcam.reset();
        // $('#my_camera').prepend('<h6 class="font-weight-bold">Camera</h6>');
        Webcam.attach('#my_camera');
        //$('video').attr('class','col-12');
        get_camera_count();
    }

    var cams_list = []; // Camera List
	var cams_switch = 0; // Switch Camera

	navigator.mediaDevices.enumerateDevices().then(function(devices) {
        devices.forEach(function(device) {
            if(device.kind == 'videoinput') {
                cams_list.push(device.deviceId);
            }
        });
	}).catch(function(err) {
	    console.log(err.name + ": " + err.message);
    });
    
     // Switch Camera
	function SwitchCam() {
		if(cams_list.length > 0) {            
            cams_switch++;
			if (cams_switch == cams_list.length) {
				cams_switch = 0;
            }            
            Webcam.set('constraints',{
				deviceId: { exact: cams_list[cams_switch] }
            });
            setup();
            $('#results').html('');
		} else {
            hide('SWITCH_CAM');
           // setup();
		}
	}
    
    // Get No. of Camera's
    function get_camera_count() {
        if(cams_list.length > 1) {
            show('SWITCH_CAM');
		} else {
            hide('SWITCH_CAM');     
		}
    }


    function capturePhoto() {

        hide('my_camera');
        show('tryPhoto');
        show('try_caption');
        hide('CAPTURE_PHOTO');
        show('nextBtn');
        hide('SWITCH_CAM');


        Webcam.snap( function(data_uri) {

            document.getElementById('WEBCAM_IMAGE').value = data_uri;
            document.getElementById('results').innerHTML =  '<img src="'+data_uri+'" style="width:100%; max-width:300px;"/>';
            show('results');
            hide('my_camera');
        
        });

    }

    function getCamera() {

        //navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.mediaDevices;
        navigator.mediaDevices.getUserMedia = navigator.mediaDevices.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.mediaDevices;

        var errorCallback = function(e) {
            hide('CAPTURE_PHOTO');
            show('camError');
        };

        /*
        navigator.getUserMedia({
            video: true,
            audio: false
        }, function(localMediaStream) {
            hide('camError');
            setup();
        }, errorCallback);
        */

       navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        }).then(stream => {
            stream.getTracks().forEach(function (track) {
              track.stop();
            });
            hide('camError');
            setup();
        })
        .catch(error => {
            hide('CAPTURE_PHOTO');
            show('camError');
        });

    }

    //Encode blob
	 function dataURLtoBlob(dataURL) {
		var BASE64_MARKER = ';base64,';
		if (dataURL.indexOf(BASE64_MARKER) == -1) {
			var parts = dataURL.split(',');
			var contentType = parts[0].split(':')[1];
			var raw = decodeURIComponent(parts[1]);

			return new Blob([raw], {
				type: contentType
			});
		}
		var parts = dataURL.split(BASE64_MARKER);
		var contentType = parts[0].split(':')[1];
		var raw = window.atob(parts[1]);
		var rawLength = raw.length;
		var uInt8Array = new Uint8Array(rawLength);
		for (var i = 0; i < rawLength; ++i) {
			uInt8Array[i] = raw.charCodeAt(i);
		}
		return new Blob([uInt8Array], {
			type: contentType
		});
	}

    //Upload File
    function upload_livePhoto(file_id,file_mode) {

        loader_start();
        disable('nextBtn');

        //var CUSTOMER_ID = $('#id').val();
       // var FILE_UPLD_FLAG = $('#FILE_UPLD_FLAG').val();

        if(!file_id) { file_id = ""; }
        if(!file_mode) { file_mode = ""; }

        var upload_data = $('#'+file_id).val();
        var blob = dataURLtoBlob(upload_data);

        var formData = new FormData();
        formData.append('cmd','ass_service_camera');
        formData.append('asnVal',$('#asnVal').val()); //ref no
        // formData.append('preData', preData); //ref no
        formData.append('FILE_MODE',file_mode); //mode- live photo , gpc
       // formData.append('FILE_UPLD_FLAG',FILE_UPLD_FLAG);
        formData.append('UPLOAD_FILE',blob,'customer.jpeg'); //file data
        formData.append('token','<?php echo $_SESSION['APP_TOKEN']; ?>');


        $.ajax({
            type: 'POST',
            enctype: 'multipart/form-data',
            url: "post/data-post",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (result) {
                $('#result2').html(result);
            },
            error: function (result) {
                loader_stop();
                enable('nextBtn');
                alert('Error : Unable to process request, Please try again.');
            }
        });

    }


    $(document).ready(function(){
        getCamera(); 
    });
</script>

