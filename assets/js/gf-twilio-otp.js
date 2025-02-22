jQuery(document).ready(function ($) {
	var selectedForms = gf_twilio_otp.forms;
	var resend_interval = gf_twilio_otp.resend_interval;

	//console.log(selectedForms);

	selectedForms.forEach(function (formData) {
		var formId = formData.form_id;
		var phoneFieldId = formData.phone_field_id;

		const phoneInputField = document.querySelector(
			"#input_" + formId + "_" + phoneFieldId
		);

		const phoneInput = window.intlTelInput(phoneInputField, {
			utilsScript:
				"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
		});

		if ($("#gform_" + formId).length > 0) {
			// Handle send OTP button click
			$(document).on("click", "#gf_send_otp_button_" + formId, function (e) {
				e.preventDefault();
				var phoneField = $("#input_" + formId + "_" + phoneFieldId).val();
				sendOtp(formId, phoneField);
			});

			// Handle resend OTP link click
			$(document).on("click", ".resend-otp", function (e) {
				e.preventDefault();
				var phoneField = $("#input_" + formId + "_" + phoneFieldId).val();
				sendOtp(formId, phoneField);
			});

			// Handle OTP verification
			$(document).on("click", "#gf_verify_otp_button", function () {
				var otpField = $("#gf_otp_input").val();
				var phoneField = $("#input_" + formId + "_" + phoneFieldId).val();

				$.ajax({
					url: gf_twilio_otp.ajax_url,
					type: "POST",
					data: {
						action: "gf_verify_otp",
						otp: otpField,
						phone_number: phoneField,
						security: gf_twilio_otp.nonce,
					},
					success: function (response) {

						if (response.success) {
							$("#gf_otp_error").hide();
							$("#gf_otp_message").text("OTP verified successfully.").show();

							setTimeout(function() {
								$("#gf_otp_popup").hide();
								$("#gf_send_otp_button_" + formId).hide();
								$("#gform_submit_button_" + formId).show();
								$("#gform_submit_button_" + formId).removeClass("gf_twilio_otp_hidden");
							}, 1500);
							
						} else {
							$("#gf_otp_error").text("Invalid OTP. Please try again.").show();
						}
					},
				});
			});
		}
	});

	function sendOtp(formId, phoneField) {
		$.ajax({
			url: gf_twilio_otp.ajax_url,
			type: "POST",
			data: {
				action: "gf_send_otp",
				phone_number: phoneField,
				security: gf_twilio_otp.nonce,
			},
			success: function (response) {
				if (response.success) {
					// Show OTP popup
					$("#gf_otp_popup").show();
					$("#gf_otp_error").hide();
					$("#gf_otp_message").hide();

					// Initialize the timer
					startOtpTimer(resend_interval);
				} else {
					alert("Failed to send OTP. Please try again.");
				}
			},
		});
	}

	function startOtpTimer(duration) {
		var timer = duration * 60; // Convert minutes to seconds
		var interval = setInterval(function () {
			var minutes = Math.floor(timer / 60);
			var seconds = timer % 60;

			minutes = minutes < 10 ? "0" + minutes : minutes;
			seconds = seconds < 10 ? "0" + seconds : seconds;

			$("#gf_otp_timer").text("OTP expires in: " + minutes + ":" + seconds);

			if (--timer < 0) {
				clearInterval(interval);
				$("#gf_otp_timer").text("");
				$(".resend-wrap").show(); // Show resend link after timer expires
			}
		}, 1000);

		// Hide resend link initially
		$(".resend-wrap").hide();
	}
});
