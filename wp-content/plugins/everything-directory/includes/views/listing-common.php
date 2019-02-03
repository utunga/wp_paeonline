<?php

//some useful functions
if (!function_exists( 'services_html')) {

    function services_html($services) {
        $comma_separated = implode(', ', array_map(function($i) {  return $i->name; }, $services));
        return $comma_separated;
    }

    function post_tags($listing) {
        return wp_get_post_tags($listing->ID);
    }

    function ed_the_last_updated() {
        $updated_date = get_the_modified_time('F jS, Y');
        //$updated_time = get_the_modified_time('h:i a');
        return 'On '. $updated_date; // . ' at '. $updated_time;
    } 

    function itemInDiv($item, $className="") {
        if (trim($item)) {
           return sprintf( '<div class="%s">%s</div>',$className, $item);  
        }
        return "";
    }

    function itemsInDiv($items, $className="") {
        $has_content = array_any("not_empty", $items);

        $content = "";
        if ($has_content) {
            $content .=  sprintf('<div class="%s">',$className);  
            foreach($items as $item) {
                $content .=  itemInDiv($item);
            }
            $content .=  sprintf('</div>');  
        }
        return $content;
    }

    function the_post_tags($listing) {
        $tmp = "";
        foreach(post_tags($listing) as $tag) {
            $tmp .= sprintf( '<span class="tag">%s</span>', $tag->name );  
        }
        return $tmp;
    }
}

?>
