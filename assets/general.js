function dragField(field, event) {
	event.dataTransfer.setData('field', field.id);
}

function dropField(target, event) {
	var field = event.dataTransfer.getData('field');
	target.appendChild(document.getElementById(field));
}
(function($) {
	$(document).ready(function() {
		$("#fields_form").submit(function() {

			var post_active_fields = $(".active-fields").children('span').map(function() {
				return $(this).attr("id");
			}).get().join(",");

			var post_inactive_fields = $(".inactive-fields").children('span').map(function() {
				return $(this).attr("id");
			}).get().join(",");

			$.post("../wp-content/plugins/coderbits-profiler/assets/ajax.php", {
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

		$("#options_form").submit(function() {

			var post_options_visual_theme = $("#coderbits_profiler_options_visual_theme").map(function() {
				return $(this).val();
			}).get().join(",");

			var post_options = post_options_visual_theme;

			$.post("../wp-content/plugins/coderbits-profiler/assets/ajax.php", {
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