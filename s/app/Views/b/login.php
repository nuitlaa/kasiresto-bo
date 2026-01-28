<!DOCTYPE html> 
<html lang="en"> 
	<head><base href="../../../">
		<title><?=sys('app-name')?></title>
		<meta charset="utf-8" />
		<meta name="description" content="<?=sys('app-description')?>" />
		<meta name="keywords" content="<?=sys('app-keyword')?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="<?=sys('app-name')?>" />
		<meta property="og:url" content="<?=site_url()?>" />
		<meta property="og:site_name" content="<?=sys('app-name')?>" /> 
		<link rel="shortcut icon" href="<?=base_url('f/'.sys('app-icon'))?>" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="<?=base_url('t/')?>plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?=base_url('t/')?>css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
	</head> 
	<body id="kt_body" class="bg-body">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(<?=base_url("t/")?>assets/media/illustrations/sigma-1/14.png)">
				<!--begin::Content-->
				<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20"> 
					<a href="<?=site_url()?>" class="mb-12"><img alt="Logo" src="<?=base_url('f/'.sys('app-logo'))?>" class="h-40px" /></a> 
					<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
						<form class="form w-100" novalidate="novalidate" id="signin_form" action="#">
							<div class="text-center mb-10"><h1 class="text-dark mb-3">Login <?=sys('app-name')?></h1></div>
							<div class="fv-row mb-10">
								<label class="form-label fs-6 fw-bolder text-dark">Username</label>
								<input class="form-control form-control-lg form-control-solid" type="text" name="username" autocomplete="off" />
							</div>
							<div class="fv-row mb-10">
								<div class="d-flex flex-stack mb-2">
									<label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
								</div>
								<input class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off" />
							</div>
							<div class="text-center">
								<!--begin::Submit button-->
								<div  id="signin" class="btn btn-lg btn-primary w-100 mb-5">
									<span class="indicator-label">Continue</span>
									<span class="indicator-progress">Please wait...
									<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
								</div>
							</div> 
						</form> 
					</div> 
				</div>  
			</div> 
		</div> 
		<script>var hostUrl = "assets/";</script>
		<!--begin::Javascript-->
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="<?=base_url('t/')?>plugins/global/plugins.bundle.js"></script>
		<script src="<?=base_url('t/')?>js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<script>
			$("#signin").click(function(){
				$("#signin").attr('data-kt-indicator','on').removeClass('btn-primary').addClass('btn-secondary');
			    $.ajax({
			        url: "<?=site_url('login')?>",
			        type: "POST",
			        data: $("#signin_form").serialize(), // send all input values
			        success: function(response){
			            console.log(response);
			            $("#signin").removeAttr('data-kt-indicator').removeClass('btn-secondary').addClass('btn-primary');
			            var y = JSON.parse(response);
			            if (y.status==true) {
			            	location.replace("<?=site_url()?>");
			            } else {
			            	Swal.fire({
						        text: y.message,
						        icon: "danger",
						        buttonsStyling: false,
						        confirmButtonText: "Ok",
						        customClass: {
						            confirmButton: "btn btn-primary"
						        }
						    });
			            }
			        }
			    });
			    	
			})
		</script>
	</body> 
</html>