<?php
/*   
* --- Recipe Calulator ---  
* Design to extract input recipe values (fridge.csv) 
* And determine what is the most appropriate recipe
*
*/
	$recipes = file_get_contents("recipe.json");
	$fridge_items_input = file("fridge.csv");
	$fridge_item = Array();
	$fridge_categories = Array('item', 'amount', 'unit', 'date');

	//Loop through inputs, extract the values into an array.
	foreach ($fridge_items_input as $field => $item) {
		$product = explode(',', $item);
		$fridge_item[] = array_combine($fridge_categories, array_values($product));
	}

	print_r($fridge_item);

?>
