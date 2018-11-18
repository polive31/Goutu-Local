<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Recipe_Metadata {

    public function get_metadata( $recipe )
    {
        if( is_feed() ) {
            return '';
        }
        
        $metadata = $this->get_metadata_array( $recipe );
        $metadata = $this->sanitize_metadata( $metadata );
        return '<script type="application/ld+json">' . json_encode( $metadata ) . '</script>';
    }

    private function sanitize_metadata( $metadata ) {
		$sanitized = array();
		if ( is_array( $metadata ) ) {
			foreach ( $metadata as $key => $value ) {
				$sanitized[ $key ] = $this->sanitize_metadata( $value );
			}
		} else {
			$sanitized = strip_shortcodes( wp_strip_all_tags( $metadata ) );
		}
		return $sanitized;
	}

    private function get_metadata_array( $recipe )
    {
        $post_id = get_the_id();
        $recipe = new Custom_WPURP_Recipe( $post_id );

        // Essentials
        $metadata = array(
            '@context' => 'http://schema.org/',
            '@type' => 'Recipe',
            'name' => $recipe->title(),
            'author' => array(
                '@type' => 'Person',
                'name' => $recipe->author(),
            ),
            'datePublished' => $recipe->date(),
            'image' => $recipe->image_url( 'full' ),
            // 'description' => $description,
            'description' => $recipe->description_meta(),
        );


        // Yield
        if( $recipe->servings() ) $metadata['recipeYield'] = $recipe->servings() . ' ' . $recipe->servings_type();


        // Rating
				//$metadata['aggregateRating'] = do_shortcode('[json-ld-rating]');
				$rating_stats = explode(' ', do_shortcode('[json-ld-rating]') );
        
        if ( isset($rating_stats) && $rating_stats[1]!=0) {
	        $metadata['aggregateRating'] = array(
	            '@type' => 'AggregateRating',
	            'ratingValue' => $rating_stats[0],
	            'ratingCount' => $rating_stats[1],
	        );
        }

        // Times
        if( $recipe->get_time_meta('prep') && $recipe->get_time_meta('cook') ) {
            // Only use separate ones when we have both
            $metadata['prepTime'] = $recipe->get_time_meta('prep');
            $metadata['cookTime'] = $recipe->get_time_meta('cook');
        } else {
            // Otherwise use total time
            if( $recipe->get_time_meta('prep') ) $metadata['totalTime'] = $recipe->get_time_meta('prep');
            if( $recipe->get_time_meta('cook') ) $metadata['totalTime'] = $recipe->get_time_meta('cook');
        }

        // Nutrition
        if( WPUltimateRecipe::is_addon_active( 'nutritional-information' ) ) {
            $nutritional = $recipe->nutritional();
            $nutritional_units = WPUltimateRecipe::addon( 'nutritional-information' )->fields;
            $nutritional_units['unsaturated_fat'] = 'g';

            $mapping = array(
                'calories' => 'calories',
                'fat' => 'fatContent',
                'saturated_fat' => 'saturatedFatContent',
                'unsaturated_fat' => 'unsaturatedFatContent',
                'trans_fat' => 'transFatContent',
                'carbohydrate' => 'carbohydrateContent',
                'sugar' => 'sugarContent',
                'fiber' => 'fiberContent',
                'protein' => 'proteinContent',
                'cholesterol' => 'cholesterolContent',
                'sodium' => 'sodiumContent',
            );

            // Unsaturated Fat = mono + poly
            if( isset( $nutritional['monounsaturated_fat'] ) && $nutritional['monounsaturated_fat'] !== '' ) {
                $nutritional['unsaturated_fat'] = floatval( $nutritional['monounsaturated_fat'] );
            }

            if( isset( $nutritional['polyunsaturated_fat'] ) && $nutritional['polyunsaturated_fat'] !== '' ) {
                $mono = isset( $nutritional['unsaturated_fat'] ) ? $nutritional['unsaturated_fat'] : 0;
                $nutritional['unsaturated_fat'] = $mono + floatval( $nutritional['polyunsaturated_fat'] );
            }

            // Get metadata
            $metadata_nutrition = array(
                '@type' => 'NutritionInformation',
                'servingSize' => '1 serving',
            );

            foreach( $mapping as $field => $meta_field ) {
                if( isset( $nutritional[$field] ) && $nutritional[$field] !== '' ) {
                    $metadata_nutrition[$meta_field] = floatval( $nutritional[$field] ) . ' ' . $nutritional_units[$field];
                }
            }

            if( count( $metadata_nutrition ) > 2 ) {
                $metadata['nutrition'] = $metadata_nutrition;
            }
        }


        // Ingredients
        if( $recipe->has_ingredients() ) {
            $metadata_ingredients = array();

            foreach( $recipe->ingredients() as $ingredient ) {
                // $metadata_ingredient = $ingredient['amount'] . ' ' . $ingredient['unit'] . ' ' . $ingredient['ingredient'];
                $metadata_ingredient = Custom_WPURP_Ingredient::display( $ingredient );
                if( trim( $ingredient['notes'] ) !== '' ) {
                    $metadata_ingredient .= ' (' . $ingredient['notes'] . ')';
                }

                $metadata_ingredients[] = $metadata_ingredient;
            }

            $metadata['recipeIngredient'] = $metadata_ingredients;
        }


        // Instructions
        if( $recipe->has_instructions() ) {
            $metadata_instructions = array();
            $previous_group = '';
            $instructions = $recipe->instructions();

            $metadata_key=-1;
            foreach( $instructions as $key=>$instruction ) {
                if( !empty( $instruction['group'] ) ) {
                    if ( $instruction['group'] != $previous_group ) {
                        // Group start, create section
                        $metadata_instructions[] = array(
                            '@type' => 'HowToSection',
                            'name'  => $instruction['group'],
                            'itemListElement' => array()
                        );
                        $previous_group = $instruction['group'];
                        $metadata_key++;
                    }
                    // Aggregate instructions to the group
                    $metadata_instructions[$metadata_key]['itemListElement'][] = array(
                        '@type' => 'HowToStep',
                        'text'  => $instruction['description']
                    );
                }
                elseif( !empty( $instruction['description'] ) ) {
                    // Top level instruction
                    $metadata_instructions[] = array(
                        '@type' => 'HowToStep',
                        'text'  => $instruction['description']
                    );
                    $metadata_key++;
                } 
            }

            $metadata['recipeInstructions'] = $metadata_instructions;
        }


        // Category & Cuisine
        $courses = wp_get_post_terms( $recipe->ID(), 'course', array( 'fields' => 'names' ) );
        if( !is_wp_error( $courses ) && isset( $courses[0] ) ) {
            $metadata['recipeCategory'] = $courses[0];
        }

        $cuisines = wp_get_post_terms( $recipe->ID(), 'cuisine', array( 'fields' => 'names' ) );
        if( !is_wp_error( $cuisines ) && isset( $cuisines[0] ) ) {
            $metadata['recipeCuisine'] = $cuisines[0];
        }
        
        $diets = wp_get_post_terms( $recipe->ID(), 'diet', array( 'fields' => 'names' ) );
        if( !is_wp_error( $diets ) && isset( $diets[0] ) ) {
            $metadata['suitableForDiet'] = $diets[0];
        }

        // Keywords
        $season = wp_get_post_terms( $recipe->ID(), 'season', array( 'fields' => 'names' ) );
        if( !is_wp_error( $season ) && isset( $season[0] ) ) {
            $metadata['keywords'] = $season[0] . ',';
        }
        $occasion = wp_get_post_terms( $recipe->ID(), 'occasion', array( 'fields' => 'names' ) );
        if( !is_wp_error( $occasion ) ) {
            $metadata['keywords'] .= implode(',',$occasion) . ',';
        }        
        $tag = wp_get_post_terms( $recipe->ID(), 'post_tag', array( 'fields' => 'names' ) );
        if( !is_wp_error( $tag ) ) {
            $metadata['keywords'] .= implode(',',$tag);
        }         

        // Allow external filtering of metadata
        return apply_filters( 'wpurp_custom_recipe_metadata', $metadata, $recipe );
    } 
    
}