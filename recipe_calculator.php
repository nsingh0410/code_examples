<?php
/*   
* --- Recipe Calulator ---  
* Design to extract input recipe values (fridge.csv) 
* And determine what is the most appropriate recipe
*
*/
 ?>
 <h1>Import CSV File</h1>
 <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data"> 
 	Import File : <input type='file' name='sel_file' size='20'> 
 	<input type='submit' name='submit' value='import'> 

 	<h1>Import Recipe File</h1>
 	Import File : <input type='file' name='sel_file_json' size='20'> 
 	<input type='submit' name='submit' value='import'> 

 <?php
		if (isset($_POST['submit']) === TRUE) {
			$fridge_items = Array();
			$filename = $_FILES['sel_file']['tmp_name']; 
			
			if (empty($_FILES['sel_file_json']['name']) === FALSE) {
				$json_recipes = file_get_contents($_FILES['sel_file_json']['name']);
			} else {
				echo 'Order Takeout';
			}
			
			if (empty($_FILES['sel_file']['tmp_name']) === FALSE) {
				$handle = fopen($filename, "r");
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$ingredient = Array();
				$ingredient['item'] = $data[0];
				$ingredient['amount'] = $data[1];
				$ingredient['unit'] = $data[2];
				$ingredient['date'] = $data[3];
				$fridge_items[] = $ingredient;
			}

			fclose($handle);
			$recipes = json_decode($json_recipes);
			find_ingredients($recipes, $fridge_items);
			print_recipe($recipes);
			
			} else {
				echo 'No CSV list';
			}
			
		}//end submit 


function closest_used_by(&$recipe, $fridge_ingredient_date)
{

	$used_by = date('d/m/Y');

	if (isset($recipe->date) === FALSE) {
		$recipe->date = $fridge_ingredient_date;
	} else {
		$prev_date_diff = abs($recipe->date - $used_by);
		$new_date_diff = abs(strtotime($fridge_ingredient_date) - strtotime($used_by));
		
		if ($new_date_diff < $prev_date_diff) {
			$recipe->date = $fridge_ingredient_date;
		}
	}

}

function find_ingredients(&$recipes, $fridge_items)
{
	// loop through all the recipe ingredients and unset the ingredients in the fridge list.
	foreach ($recipes as &$recipe) {
		foreach ($recipe->ingredients as $key => $recipe_ingredient) {
			foreach ($fridge_items as $items => $fridge_ingredient) {
				if ($fridge_ingredient['item'] === $recipe_ingredient->item && $fridge_ingredient['unit'] === $recipe_ingredient->unit) {
					if (isset($fridge_ingredient['date'])) {

						// add the 
						closest_used_by($recipe, $fridge_ingredient['date']); 
					}	
					if ($fridge_ingredient['item'] >= $recipe_ingredient->amount) {
						if (isset($recipe_ingredient->item)) {
							unset($recipe->ingredients[$key]);
						}
					}
				} 
			}
		}
	}//end loop recipes
}//end find_ingredients()

function print_recipe($recipes)
{
	// print the recipe closest to used by date
	$prev_date = '';
	$used_by = date('d/m/Y');
	$closest = FALSE; 

	foreach ($recipes as &$recipe) {
		if (empty($recipe->ingredients) === TRUE) {
			$matched = Array();
			
		$prev_date_diff = abs($prev_date - $used_by);
		$new_date_diff = abs($recipe->date - $used_by);
			
			if ($new_date_diff < $prev_date_diff) {
				$matched[$recipe->name] = $recipe->date;
			} else {
				$matched[$recipe->name] = $prev_date;
			}
			$prev_date = $recipe->date;
		}
	}//end loop recipes
	
	echo key($matched);
	
}//end print_recipe()





 	
		

		
		
	
?>