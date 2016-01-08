function joinGroup(groupId) {
    $.ajax({
       url: 'includes/groups/joinGroup.php',
       type: 'POST',
       data: {group_id: groupId},
       success: function(data) {
           console.log(data);
       }, 
       error: function(data) {
           console.log(data);
       }
    });
}

function leaveGroup(groupId) {
    $.ajax({
        url: 'includes/groups/leaveGroup.php',
        type: 'POST',
        data: {group_id: groupId},
        success: function(data) {
            console.log(data);
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function toggleReplyVisibility(replyId, noteId) {
	// Basic logic that 1. Hides all reply fields, then shows the one that is wanted.
	if ($(replyId).is(":visible")) {
		$(".board_reply").hide();
		$(replyId).hide();
		$(noteId).show();
	} else {
		// Or 2. div clichides all reply fields and shows and notes underneath. 
		$(".board_reply").hide();
		$(replyId).show();
		$(".reply_note").show();
		$(noteId).hide();
	}
}

function reloadReplies(encodedMessageId, message) {
	$.ajax({
		url: '../includes/schools/getBoardMessageReplies.php',
		type: 'POST',
		data: {message_id: encodedMessageId},
		dataType: 'json',
		success: function(data) {
			var replies = $(message).find(".replies");
			replies.empty();
			
			data.forEach(function(entry) {
				replies.append(formReplyString(entry));
			});
		},
		error: function(data) {
			console.log(data);
		}
	});
}

function formReplyString(reply) {
	console.log(reply);
	var author = reply.author;
	var name = author.firstName + ' ' + author.lastName;
	
	return '<li><h5>' + reply.body + '</h5>' +
		   '<p><a href="user.php?id=' + reply.id + '">' +
		   '<img id="user_profile" width="25" height="25" src="' + author.image + '"/>' + name + '</a>' +
		   ' on ' + reply.dt + ' </p></li>';
}

$(document).ready(function() {
	$('.board_reply').click(function(e) {
		e.stopPropagation();
	});
	
	$('.board_reply').submit(function(e) {
		e.preventDefault();
	
		$(this).find(".reply_button").click();
	});
	
	$(".reply_button").click(function() {
		var formData = new FormData();
		var encodedMessageId = $(this.form[1]).val();
		var replyButton = $(this);
		
		formData.append('body', $(this.form[0]).val());
		formData.append('message_id', encodedMessageId);
		$(this.form[0]).val('');
		toggleReplyVisibility($(this.form).attr("id"), $(this.form).siblings(".reply_note").attr("id"));
		
		$.ajax({
			url: '../includes/groups/createBoardMessageReply.php',
			type: 'POST',
			data: formData,
			dataType: 'json',
			processData: false,
			contentType: false,
			success: function(data) {
				console.log(data);
				// reloadReplies(encodedMessageId, replyButton.closest(".message"));
				replyButton.closest(".message").find(".replies").append(formReplyString($.extend({}, data)));
			},
			error: function(data) {
				console.log(data);
			}
		});
	});
});
