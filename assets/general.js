function dragField(field, event) {
    event.dataTransfer.setData('field', field.id);
}
function dropField(target, event) {
    var field = event.dataTransfer.getData('field');
    target.appendChild(document.getElementById(field)); 
}

var $j = jQuery.noConflict();
 
$j(document).ready(function() {

	var post_active_fields, post_inactive_fields;

	post_active_fields = $j(".active_fields").find(".field");
	post_inactive_fields = $j(".inactive_fields").find(".field");

    $j("#fields_form").submit(function(){
    	$j.post("assets/ajax.php", {
    		action: "save_fields",
    		active_fields : active_fields,
    		inactive_fields : inactive_fields,
    		rand: Math.random()
    	}, function(data){

    	});

	    // Not to post the form physically
		return false;
    });

});