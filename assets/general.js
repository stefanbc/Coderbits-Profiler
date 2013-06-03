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
    	$j.post("assets/ajax.php", {
    		action: "save_fields",
    	}, function(data){

    	});

	    // Not to post the form physically
		return false;
    });
});