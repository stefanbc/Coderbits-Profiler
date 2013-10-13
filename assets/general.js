
// Drag a field
function dragField(field, event) {
	event.dataTransfer.setData('field', field.id);
}

// Drop a field
function dropField(target, event) {
	var field = event.dataTransfer.getData('field');
	target.appendChild(document.getElementById(field));
}

(function($) {
	$(document).ready(function() {
		// If the form is submited 
		$("#fields_form").submit(function() {

			// Get the active fields
			var post_active_fields = $(".active-fields").children('span').map(function() {
				return $(this).attr("id");
			}).get().join(",");

			// Get the inactive fields
			var post_inactive_fields = $(".inactive-fields").children('span').map(function() {
				return $(this).attr("id");
			}).get().join(",");

			// Save to the DB
			$.post(AJAX_FILE, {
				action: "save_fields",
				active_fields: post_active_fields,
				inactive_fields: post_inactive_fields,
				rand: Math.random()
			}, function(data) {
				if (data == 'Yes') {
					$('#fields_submit_button').append('<span class="update">Fields updated! Refresh the page to see the new changes.</span>');
					setTimeout(function() {
						$('#fields_submit_button .update').remove();
					}, 3000);
				} else {
					console.log(data);
				}
			});
			return false;
		});

		// If the form is submited
		$("#options_form").submit(function() {

			// Get the theme selected
			var post_options_visual_theme = $("#coderbits_profiler_options_visual_theme").map(function() {
				return $(this).val();
			}).get().join(",");

			// Save to options
			var post_options = post_options_visual_theme;

			// Save to the DB
			$.post(AJAX_FILE, {
				action: "save_options",
				plugin_options: post_options,
				rand: Math.random()
			}, function(data) {
				if (data == 'Yes') {
					$('#options_submit_button').append('<span class="update">Options updated! Refresh the page to see the new changes.</span>');
					setTimeout(function() {
						$('#options_submit_button .update').fadeOut(500).remove();
					}, 5000);
				} else {
					console.log(data);
				}
			});
			return false;
		});

	});
})(jQuery)