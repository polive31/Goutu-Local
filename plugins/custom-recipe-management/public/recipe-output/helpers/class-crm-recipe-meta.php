<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Recipe_Meta {

    private $post_id;
    private $recipe;

    public function enqueue_recipe_meta($meta)
    {
        $Recipe_Meta = CSD_Meta::get_instance('recipe');
        if (!$Recipe_Meta->is_output_here()) return;

        // Prepare data array
        $this->post_id = get_the_id();
        $post = get_post();
        $this->recipe = new CRM_Recipe($this->post_id);

        if (class_exists( 'CSR_Rating' ))
            $rating = CSR_Rating::get_post_stats( $this->post_id );
        else
            $rating=array();

        $data = array(
            'type'              => 'Recipe',
            'title'             => get_the_title(),
            'url'               => get_permalink(),
            'date-published'    => get_the_date(DATE_ATOM),
            'date-modified'     => get_post_modified_time(DATE_ATOM),
            'thumbnail'         => get_the_post_thumbnail_url(),
            'tags'              => wp_get_post_tags(),
            'author'            => ucfirst(get_the_author_meta('user_nicename', $post->post_author )),
            'description'       => $this->get_description(),
            'servings'          => $this->recipe->servings() . ' ' . $this->recipe->servings_type(),
            'rating-value'      => !empty($rating['rating'])? $rating['rating']: rand(3.5,5),
            'rating-count'      => !empty($rating['votes'])? $rating['votes']: rand(1,5),
            'course'            => $this->get_course(),
            'cuisine'           => $this->get_cuisine(),
            'diet'              => $this->get_diet(),
            'cooktime'          => $this->recipe->get_time_meta('cook'),
            'preptime'          => $this->recipe->get_time_meta('prep'),
            'totaltime'         => $this->recipe->get_total_time_meta(),
            'ingredients'       => $this->get_ingredients(),
            'instructions'      => $this->get_instructions(),
            'reviews'           => $this->get_reviews(),
            'tags'              => $this->get_tags(),
        );
        $Recipe_Meta->set($meta, $data);
        return $meta;
    }

    // Description output in json meta
    // In this case, we output the description based on either scheme : description meta
    // or as part of the post's content (new scheme)
    public function get_description() {
        $description = get_post_meta($this->post_id, '_yoast_wpseo_metadesc', true);
        if (empty($description))
            $description = $this->recipe->post_content();
        if (empty($description))
            $description = $this->recipe->description();
        if (empty($description))
            $description='';
        //     $post = get_post( $this->recipe->ID() );
        //     $content = $post?$post->post_content:'';
        //     $description = trim(preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content));
        // }
        return $description;
    }

    private function get_course()
    {
        $course = '';
        $courses = wp_get_post_terms($this->recipe->ID(), 'course', array('fields' => 'names'));
        if (!is_wp_error($courses) && isset($courses[0])) {
            $course = $courses[0];
        }
        return $course;
    }

    private function get_cuisine()
    {
        $cuisine = 'France';
        $cuisines = wp_get_post_terms($this->recipe->ID(), 'cuisine', array('fields' => 'names'));
        if (!is_wp_error($cuisines) && isset($cuisines[0])) {
            $cuisine = $cuisines[0];
        }
        return $cuisine;
    }

    private function get_reviews() {
        $reviews=array();
        $comments = get_comments( array(
            'post_id'   => $this->recipe->ID(),
            'status'    => 'approve',
        ));
        foreach ($comments as $comment) {
            $name=$comment->comment_author;
            $body=$comment->comment_content;
            $rating=class_exists('CSR_Rating')?CSR_Rating::get_comment_rating($comment->comment_ID):0;
            $reviews[]=$this->get_review( $name, $body, $rating );
        }
        return $reviews;
    }

    private function get_review( $name, $body, $rating) {
        $template = array(
            '@type'             => 'Review',
            'author'            => array(
                '@type'         => 'Person',
                'name'          => $name,
            ),
            'reviewBody'        => $body,
        );
        if ($rating) {
            $template['reviewRating']=array(
                '@type'         => 'Rating',
                'ratingValue'   => $rating,
            );
        }
        return $template;
    }

    private function get_ingredients() {
        $ingredients = array();
        if( $this->recipe->has_ingredients() ) {
            foreach( $this->recipe->ingredients() as $ingredient ) {
                $notes = $ingredient['notes'];
                unset($ingredient['notes']);
                $parts = CRM_Ingredient::get_ingredient_parts( $ingredient );
                if( trim( $notes ) !== '' ) {
                    $notes = ' (' . trim($notes) . ')';
                }
                $ingredients[] = $parts['amount'] . $parts['unit'] . ' ' . $parts['of'] . ' ' . $parts['ingredient'] . ' ' . $notes;
            }
        }
        return $ingredients;
    }

    private function get_instructions()
    {
        $metadata_instructions = array();

        if( $this->recipe->has_instructions() ) {
            $previous_group = '';
            $instructions = $this->recipe->instructions();

            $metadata_key=-1;
            $step=0;
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
                    $metadata_instructions[$metadata_key]['itemListElement'][] = $this->get_howtostep($instruction,$step);
                    $step++;
                }
                elseif( !empty( $instruction['description'] ) ) {
                    // Top level instruction
                    $metadata_instructions[] = $this->get_howtostep( $instruction,$step);
                    $metadata_key++;
                    $step++;
                }
            }
        }
        return $metadata_instructions;
    }

    public function get_howtostep( $instruction, $step ) {
        $step = array(
            '@type' => 'HowToStep',
            'text'      => $instruction['description'],
            'url'       => rtrim(get_permalink(), '/') . '#' . CRM_Assets::RECIPE_STEP_ID_ROOT . $step,
        );
        if (!empty($instruction['image'])) {
            $image = wp_get_attachment_image_src($instruction['image'], 'full');
            $step['image']=$image[0];
        }
        return $step;
    }

    public function get_diet()
    {
        $diet = '';
        $diets = wp_get_post_terms( $this->recipe->ID(), 'diet', array( 'fields' => 'names' ) );
        if( !is_wp_error( $diets ) && isset( $diets[0] ) ) {
            $diet = $diets[0];
        }
        return $diet;
    }

    public function get_tags()
    {
        $tags = '';
        $season = wp_get_post_terms( $this->recipe->ID(), 'season', array( 'fields' => 'names' ) );
        if( !is_wp_error( $season ) && isset( $season[0] ) ) {
            $tags = $season[0] . ',';
        }
        $occasion = wp_get_post_terms( $this->recipe->ID(), 'occasion', array( 'fields' => 'names' ) );
        if( !is_wp_error( $occasion ) ) {
            $tags .= implode(',', $occasion) . ',';
        }
        $tag = wp_get_post_terms( $this->recipe->ID(), 'post_tag', array( 'fields' => 'names' ) );
        if( !is_wp_error( $tag ) ) {
            $tags .= implode(',',$tag);
        }
        return $tags;
    }


       // Nutrition
        // if( WPUltimateRecipe::is_addon_active( 'nutritional-information' ) ) {
            // $nutritional = $this->recipe->nutritional();
            // $nutritional_units = WPUltimateRecipe::addon( 'nutritional-information' )->fields;
            // $nutritional_units['unsaturated_fat'] = 'g';

            // $mapping = array(
            //     'calories' => 'calories',
            //     'fat' => 'fatContent',
            //     'saturated_fat' => 'saturatedFatContent',
            //     'unsaturated_fat' => 'unsaturatedFatContent',
            //     'trans_fat' => 'transFatContent',
            //     'carbohydrate' => 'carbohydrateContent',
            //     'sugar' => 'sugarContent',
            //     'fiber' => 'fiberContent',
            //     'protein' => 'proteinContent',
            //     'cholesterol' => 'cholesterolContent',
            //     'sodium' => 'sodiumContent',
            // );

            // // Unsaturated Fat = mono + poly
            // if( isset( $nutritional['monounsaturated_fat'] ) && $nutritional['monounsaturated_fat'] !== '' ) {
            //     $nutritional['unsaturated_fat'] = floatval( $nutritional['monounsaturated_fat'] );
            // }

            // if( isset( $nutritional['polyunsaturated_fat'] ) && $nutritional['polyunsaturated_fat'] !== '' ) {
            //     $mono = isset( $nutritional['unsaturated_fat'] ) ? $nutritional['unsaturated_fat'] : 0;
            //     $nutritional['unsaturated_fat'] = $mono + floatval( $nutritional['polyunsaturated_fat'] );
            // }

            // // Get metadata
            // $metadata_nutrition = array(
            //     '@type' => 'NutritionInformation',
            //     'servingSize' => '1 serving',
            // );

            // foreach( $mapping as $field => $meta_field ) {
            //     if( isset( $nutritional[$field] ) && $nutritional[$field] !== '' ) {
            //         $metadata_nutrition[$meta_field] = floatval( $nutritional[$field] ) . ' ' . $nutritional_units[$field];
            //     }
            // }

            // if( count( $metadata_nutrition ) > 2 ) {
            //     $metadata['nutrition'] = $metadata_nutrition;
            // }
        // }



}
