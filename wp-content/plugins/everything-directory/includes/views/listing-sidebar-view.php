<?php

    // Redirect if all data is empty 
    include( dirname( __FILE__ ) . '/listing-common.php' );
    $contact_name = $listing->contact_name;
    $address = $listing->address;
    $phone_number = $listing->phone_number;
    $email_address = $listing->email_address;
    $opening_hours = $listing->opening_hours;
    $duration = $listing->duration;
    $map = $listing->map;
    $website = $listing->website;
    $short_description = $listing->short_description;
    $has_content = $listing->has_content;
    $is_redirect_only = $listing->is_redirect_only;
?>

<?php 

if ($is_redirect_only) { 

    // as of now 'is_redirect_only' has no effect whatsoever 
    //
    // commented out the code that used to do a redirect here 
    // see following git history for what used to be here 
    //
    //cf. https://github.com/utunga/wp_paeonline/commit/038a23ca517b102c7d4feb9145807da70ec88584

} ?>

<div class="directory-sidebar"><?php
    $location = get_field('map');
    ?>
    <div class="info-items">
    <?php 
        if( !empty($location) ) {
    ?>
        <div class="acf-map">
            <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
        </div>
    <?php } 

        if ($listing->has_content) {
    
            if (array_any("not_empty", array( $website))) {
                echo '<div class="item-lg">';  
         
                if (not_empty($website)) {
                        echo sprintf( '<div class="website"><a href="%s" target="_blank">Visit website</a></div>',$website);  
                }
                echo('</div>');  
            }

            echo itemInDiv($address, "item");
            echo itemsInDiv(array($opening_hours, $duration), "item");
            echo itemsInDiv(array($contact_name, $phone_number, $email_address), "item-sm");
        }
        ?>
        <div class="item categories">
            <?php echo genesis_post_categories_shortcode($listing, array('before' => '')); 
            ?>
        </div>

    </div>

</div>


</div>


<!-- FIXME In theory this include is needed - but it clashes with the 
     Events calendar pro google maps API !!! key so comment this one out here. 
     PHP/Wordpress is just such a clusterf**k of messy code. Argh!-->
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY ?>"></script>

