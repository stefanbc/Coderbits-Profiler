function dragField(field, event) {
    event.dataTransfer.setData('field', field.id);
}
function dropField(target, event) {
    var field = event.dataTransfer.getData('field');
    target.appendChild(document.getElementById(field)); 
}

var $j = jQuery.noConflict();
$j(document).ready(function() {

    $j("#fields_form").submit(function(){

    	var post_active_fields = $j(".active-fields").children('span').map(function() {
		    return $j(this).attr("id");
		}).get().join(",");

		var post_inactive_fields = $j(".inactive-fields").children('span').map(function() {
		    return $j(this).attr("id");
		}).get().join(",");

		console.log(post_active_fields);
		console.log(post_inactive_fields);

    	$j.post("../wp-content/plugins/coderbits/assets/ajax.php", {
    		action: "save_fields",
    		active_fields : post_active_fields,
    		inactive_fields : post_inactive_fields,
    		rand: Math.random()
    	}, function(data){
    		console.log(data);
    		if (data == 'Yes') {
    			//location.reload();
    		}
    	});

	    // Not to post the form physically
		return false;
    });

});