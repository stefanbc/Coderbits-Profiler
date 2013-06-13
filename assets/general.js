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
					$('#fields_submit_button').append('<span class="update">Fields updated!</span>');
					setTimeout(function() {
						$('#fields_submit_button .update').remove();
					}, 3000);
				} else {
                    console.log(data);
				}
			});
			return false;
		});
	});
})(jQuery)