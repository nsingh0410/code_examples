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


			$used_by = date('d/m/Y');
			// loop through all the recipe ingredients and unset the ingredients in the fridge list.
			foreach ($recipes as &$recipe) {
				foreach ($recipe->ingredients as $key => $recipe_ingredient) {
					foreach ($fridge_items as $items => $fridge_ingredient) {
						if ($fridge_ingredient['item'] === $recipe_ingredient->item && $fridge_ingredient['unit'] === $recipe_ingredient->unit) {
							if ($fridge_ingredient['item'] >= $recipe_ingredient->amount) {
								if (isset($recipe_ingredient->item)) {
									unset($recipe->ingredients[$key]);
								}
							}
						} 
					}
				}
				if (empty($recipe->ingredients) === TRUE) {
					echo $recipe->name;
				}
			}//end loop recipes
			

			//foreach ($recipes->ingredients as $key => $recipe_ingredient) {
				
			//}

			} else {
				echo 'No CSV list';
			}
			

		}//end submit 

		

		
		
	
?>