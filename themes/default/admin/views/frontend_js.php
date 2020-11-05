



	<div class="gototop js-top">

		<a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>

	</div>

	<!-- jQuery -->

	<script src="<?= $assets ?>frontend/js/jquery.min.js"></script>

	<!-- jQuery Easing -->

	<script src="<?= $assets ?>frontend/js/jquery.easing.1.3.js"></script>

	<!-- Bootstrap -->

	<script src="<?= $assets ?>frontend/js/bootstrap.min.js"></script>

	<!-- Waypoints -->

	<script src="<?= $assets ?>frontend/js/jquery.waypoints.min.js"></script>

	<!-- Main -->

	<script src="<?= $assets ?>frontend/js/main.js"></script>

	<script src="<?= $assets ?>frontend/js/jquery.validate.js"></script>

		<script type="text/javascript">

			$("#contact-form").validate({

		ignore: ".ignore",

		rules: {

			email:{required: true,email: true},

		},

		messages: {

			email:{required: "Enter your email id",email: "Invaild email id"},

		},

		submitHandler: function (form) {

			//alert('a');

			var request;

			$('#submit_contact').attr("disabled", true);

			var last = $('#contact-form').serialize();

			request =  $.ajax({

				type: 'POST',

				url: 'contact_mail_form.php',

				

				data:last,

				success: function(res) {

					setTimeout(function() {

					$('.contact_status').hide('fadeOut');

					

					},5000);

					//alert(res);

					//alert('b');

					if (res == 'successful') {

						$('.contact_status').addClass('text-success');

						$('.contact_status').html('Your message has been sent!').slideDown();

						$("#email").val('');

						window.setTimeout(update, 5000);

					}

					else {

						$('.contact_status').addClass('text-warning');

						$('.contact_status').html('Mail not sent, try again!').slideDown();

						$("#email").val('');

						$('#submit_contact').attr("disabled", false);

					} 

				}

			});

		}

	});

	jQuery.validator.addMethod("lettersonly", function(value, element) {

    return this.optional(element) || /^[a-zA-Z\s]*$/i.test(value);

	}, "type only letter and white space");

	

	

		</script>

