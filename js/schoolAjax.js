function createSchool(schoolName, location) {
	if (location != undefined) {
		$.ajax({
			url: '../includes/schools/createSchool.php',
			type: 'POST',
			data: {school_name: schoolName, location: location},
			success: function(data) {
				joinSchool(data);
			},
			error: function(data) {
				console.log(data);
			}
		});
	}
}

function joinSchool(schoolId) {
//	console.log(schoolId);
	$.ajax({
		url: '../includes/schools/joinSchool.php',
		type: 'POST',
		dataType: "json",
		data: {school_id: schoolId},
		success: function(data) {
			console.log(data);	
		},
		error: function(data) {
			console.log(JSON.parse(data));	
		}
	});
}

function leaveSchool(schoolId) {
	$.ajax({
		url: '../includes/schools/leaveSchool.php',
		type: 'POST',
		data: {school_id: schoolId},
		success: function(data) {
			console.log(data);
		}, error: function(data) {
			console.log(data);
		}
	});  
}

function searchSchools(schoolName, location) {
	$.ajax({
		url: '../includes/schools/searchSchools.php',
		type: 'POST',
		data: {school_name: schoolName, location: location},
		success: function(data) {
			$("#search_results").empty();
			JSON.parse(data).forEach(function(entry) {
				// Styling here!!
				// This is just adding a <li> element to the end. If you need to call name/location just do entry["name/location"] with +'s to concat.
				// Leave the a href alone. Add the classes you need inside the class='' (obviously)
				// Make sure to use ''s inside the ""s. (""s always go on outside)
				$("#search_results").append("<li id='search_result' class=''> \
												<a id='search_result_link' class='' href='school.php?id=" + entry["id"] + "'>" + entry["name"] + "</a> \
												<p id='search_result_extra' class=''>" + entry["location"] + "</p> \
											</li>");
			});
		},
		error: function(data) {
			$("#search_results").empty();
			console.log(data);
		}
	});
}