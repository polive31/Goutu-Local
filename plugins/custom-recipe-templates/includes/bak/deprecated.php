<?php
				$js_enqueue = $this->remove_entry($js_enqueue, 'name', 'select2wpurp');
				$js_enqueue = $this->remove_entry($js_enqueue, 'name', 'user-submissions');
				$js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/recipe_form.js');
				$js_enqueue = $this->remove_entry($js_enqueue, 'name', 'wpurp-user-menus');

				// Group #1
				$js_enqueue = $this->remove_entry($js_enqueue, 'name', 'fraction');
				$js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/adjustable_servings.js');
				$js_enqueue = $this->remove_entry($js_enqueue, 'name', 'wpurp-meal-planner');

				// Group #2
				$js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/favorite-recipes.js');	

				// Group #3
				$js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/partners.js');

				// Group #4					
				$js_enqueue = $this->remove_entry($js_enqueue, 'name', 'sharrre');				
				$js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/sharing_buttons.js');	

				
				// $js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/print_button.js');

				// $js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/responsive.js');
				// $js_enqueue = $this->remove_entry($js_enqueue, 'file', '/js/tooltips.js');	

				// echo '<pre>' . print_r($js_enqueue) . '</pre>';

				// $js_enqueue = $js_enqueue;


?>