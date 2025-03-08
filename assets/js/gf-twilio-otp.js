
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
				
				var isValid = true;
				
				// Validate required fields
				var firstName = $("#input_1_60_3").val().trim();
				var lastName = $("#input_1_60_6").val().trim();
				var email = $("#input_1_61").val().trim();
				var phoneNumber = $("#input_" + formId + "_" + phoneFieldId).val().trim();
			
				if (firstName === "") {
					$("#input_1_60_3").addClass("field-error");
					isValid = false;
				} else {
					$("#input_1_60_3").removeClass("field-error");
				}
			
				if (lastName === "") {
					$("#input_1_60_6").addClass("field-error");
					isValid = false;
				} else {
					$("#input_1_60_6").removeClass("field-error");
				}
			
				if (email === "") {
					$("#input_1_61").addClass("field-error");
					isValid = false;
				} else {
					$("#input_1_61").removeClass("field-error");
				}
			
				if (phoneNumber === "") {
					$("#input_" + formId + "_" + phoneFieldId).addClass("field-error");
					isValid = false;
				} else {
					$("#input_" + formId + "_" + phoneFieldId).removeClass("field-error");
				}
			
				if (!isValid) {
					alert("Please fill in First Name, Last Name, Email, and Phone Number before requesting OTP.");
					return; // Stop function execution
				}
			
				// Get phone number with country code
				var phoneInputField = document.querySelector("#input_" + formId + "_" + phoneFieldId);
				var phoneInputInstance = window.intlTelInputGlobals.getInstance(phoneInputField);
				var countryData = phoneInputInstance.getSelectedCountryData();
				var countryCode = countryData.dialCode;
			
				var fullPhoneNumber = "+" + countryCode + phoneNumber;
			
				// Send OTP
				sendOtp(formId, fullPhoneNumber);
			});
			
			
			

			// Handle resend OTP link click
			$(document).on("click", ".resend-otp", function (e) {
				e.preventDefault();
				var phoneField = $("#input_" + formId + "_" + phoneFieldId).val();
				sendOtp(formId, phoneField);
			});
			$(document).on("click", ".resend-otp", function (e) {
				e.preventDefault();
				
				var phoneInputField = document.querySelector("#input_" + formId + "_" + phoneFieldId);
				var phoneInputInstance = window.intlTelInputGlobals.getInstance(phoneInputField);
				var countryData = phoneInputInstance.getSelectedCountryData();
				var countryCode = countryData.dialCode; // Get the selected country code
			
				// Concatenate country code with phone number
				var fullPhoneNumber = "+" + countryCode + $("#input_" + formId + "_" + phoneFieldId).val();
			
				sendOtp(formId, fullPhoneNumber); // Pass the correctly formatted phone number
			});
			
			// Handle OTP verification
			$(document).on("click", "#gf_verify_otp_button", function () {
				var otpField = $("#gf_otp_input").val().trim(); // Get OTP input
			
				var phoneInputField = document.querySelector("#input_" + formId + "_" + phoneFieldId);
				var phoneInputInstance = window.intlTelInputGlobals.getInstance(phoneInputField);
				var countryData = phoneInputInstance.getSelectedCountryData();
				var countryCode = countryData.dialCode; // Get the selected country code
			
				// Concatenate country code with phone number
				var fullPhoneNumber = "+" + countryCode + $("#input_" + formId + "_" + phoneFieldId).val().trim().replace(/\s+/g, "");
			
				// Debugging: Check if the full phone number is correct
				console.log("Verifying OTP for:", fullPhoneNumber);
			
				$.ajax({
					url: gf_twilio_otp.ajax_url,
					type: "POST",
					data: {
						action: "gf_verify_otp",
						otp: otpField,
						phone_number: fullPhoneNumber, // Use formatted phone number
						security: gf_twilio_otp.nonce,
					},
					success: function (response) {
						console.log("OTP Verification Response:", response); // Debugging response
			
						if (response.success) {
							$("#gf_otp_error").hide();
							$("#gf_otp_message").text("OTP verified successfully.").show();
			
							setTimeout(function () {
								$("#gf_otp_popup").hide();
								$("#gf_send_otp_button_" + formId).hide();
								$("#gform_submit_button_" + formId).show();
								$("#gform_submit_button_" + formId).removeClass("gf_twilio_otp_hidden");
							}, 1500);
						} else {
							$("#gf_otp_error").text("Invalid OTP. Please try again.").show();
						}
					},
					error: function (xhr, status, error) {
						console.error("OTP Verification Error:", xhr.responseText); // Debugging errors
					}
				});
			});
			
			
		}
	});
	
	function sendOtp(formId, phoneField) {
		var isValid = true;
		// Validate only the required fields
		var firstName = $("#input_1_60_3").val().trim();
		var lastName = $("#input_1_60_6").val().trim();
		var email = $("#input_1_61").val().trim();
		var phoneNumber = $("#input_" + formId + "_62").val().trim();
	
		if (firstName === "") {
			$("#input_1_60_3").addClass("field-error");
			isValid = false;
		} else {
			$("#input_1_60_3").removeClass("field-error");
		}
	
		if (lastName === "") {
			$("#input_1_60_6").addClass("field-error");
			isValid = false;
		} else {
			$("#input_1_60_6").removeClass("field-error");
		}
	
		if (email === "") {
			$("#input_1_61").addClass("field-error");
			isValid = false;
		} else {
			$("#input_1_61").removeClass("field-error");
		}
	
		if (phoneNumber === "") {
			$("#input_" + formId + "_62").addClass("field-error");
			isValid = false;
		} else {
			$("#input_" + formId + "_62").removeClass("field-error");
		}
	
		if (!isValid) {
			alert("Please fill in First Name, Last Name, Email, and Phone Number before requesting OTP.");
			return; // Stop function execution
		}
	
		// Get country code and format phone number
		var phoneInputField = document.querySelector("#input_" + formId + "_62");
		var phoneInputInstance = window.intlTelInputGlobals.getInstance(phoneInputField);
		var countryData = phoneInputInstance.getSelectedCountryData();
		var countryCode = countryData.dialCode;
		var fullPhoneNumber = "+" + countryCode + phoneNumber;
	
		// Send OTP request
		$.ajax({
			url: gf_twilio_otp.ajax_url,
			type: "POST",
			data: {
				action: "gf_send_otp",
				phone_number: fullPhoneNumber,
				country_code: countryCode,
				security: gf_twilio_otp.nonce,
			},
			success: function (response) {
				if (response.success) {
					$("#gf_otp_popup").show();
					$("#gf_otp_error").hide();
					$("#gf_otp_message").hide();
					startOtpTimer(resend_interval);
				} else {
					alert("Failed to send OTP. Please try again.");
				}
			},
		});
	}

	function startOtpTimer(duration) {
		var timer = duration * 60; 
		var interval = setInterval(function () {
			var minutes = Math.floor(timer / 60);
			var seconds = timer % 60;

			minutes = minutes < 10 ? "0" + minutes : minutes;
			seconds = seconds < 10 ? "0" + seconds : seconds;

			$("#gf_otp_timer").text("OTP expires in: " + minutes + ":" + seconds);

			if (--timer < 0) {
				clearInterval(interval);
				$("#gf_otp_timer").text("");
				$(".resend-wrap").show(); 
			}
		}, 1000);

		// Hide resend link initially
		$(".resend-wrap").hide();
	}
});
