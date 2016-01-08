$(document).ready(function() {
	$("#setup").click(function(e) {
		var formData = new FormData($("#user_setup_form")[0]);
		
		$.ajax({
			url: 'includes/members/setupUser.php',
			type: 'POST',
			data: formData,
			async: false,
			success: function(data) {
				location.reload();
			},
			error: function(data) { 
				console.log(data);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});
});