<?php
    include( dirname( __FILE__ ) . '/listing-common.php' );
    $contact_name = $listing->contact_name;
    $address = $listing->address;
    $phone_number = $listing->phone_number;
    $email_address = $listing->email_address;
    $opening_hours = $listing->opening_hours;
    $duration = $listing->duration;
    $map = $listing->map;
    $website = $listing->website;
    $logo = $listing->logo;
    $short_description = $listing->short_description;
    $page_link = $listing->page_link;
    $has_content = $listing->has_content;
    $has_data_row = !empty($address.$opening_hours.$duration.$contact_name.$phone_number.$email_address);
    $has_description_row = !empty($short_description);
    $has_taxonomy_row = $has_content or !empty(post_tags($listing));
    $is_redirect_only = $listing->is_redirect_only;
    $disable_page_link = $listing->disable_page_link;
    $is_featured = $listing->is_featured;
    $featured_class = ($is_featured) ? "supporter" : "";

    $main_link = "";
    if (!$disable_page_link) {
        if ($has_content && !empty($page_link)) {
            $main_link = $page_link;
        }
        else if (!empty($website)) {
            $main_link = $website;
        }
    }
?>

<div class="directory-item-container <?php echo $featured_class ?>">
    <div class="directory-item ">
        <div class="directory-row title-row">
            <div class="title"><?php
                if (!empty($main_link)) {
                    if ($is_redirect_only) {
                        printf('<a class="listing_title_link" href="%s">%s <span class="website-link">➚</span></a>', $main_link, $title); 
                    }
                    else {
                        printf('<a class="listing_title_link" href="%s">%s</a>', $main_link, $title);  
                    }
                }
                else {
                    printf('%s', $title);
                }
         
            ?>
            </div>
            <?php
                if (not_empty($website) and !$is_redirect_only) {
                    echo '<div class="website">';  
                    echo sprintf( '<div class="website-link"><a href="%s" target="_blank">Visit website ➚</a></div>',$website);  
                    echo('</div>'); 
                }
            ?> 
        </div>

        <?php if ($has_data_row) { ?>
        <div class="directory-row info-items">
            <?php 
                echo itemInDiv($address, "item");
                echo itemsInDiv(array($opening_hours, $duration), "item");
                echo itemsInDiv(array($contact_name, $phone_number, $email_address), "item-sm");
            ?>
        </div>
        <?php } ?>

        <?php if ($has_description_row) { ?>
        <div class="directory-row">
            <div class="short-description">
                <?php echo $short_description ?>
            </div>
        </div>
        <?php } ?>
 
        <?php if ($has_taxonomy_row or $has_content) { ?>
        <div class="directory-row taxonomy more-button-row">
            <div class="categories">
                <?php echo genesis_post_categories_shortcode($listing, array('before' => '')); 
                ?>
            </div>
            <?php 
            if ($has_content) 
            {
            ?>
                <div class="more-button">
                <?php
                    printf( '<a href="%s">&nbsp&nbsp;&nbsp;&nbsp;&nbsp;%s&nbsp;<span class="arrows">&gt;&gt;</span></a>', $page_link, "More" );  
                ?>
                </div><?php
            }
            ?>
        </div>
        <?php } ?>

    </div>

    <?php if (!empty($logo)) { 

        //var_dump($logo);

        $sponsor_size = wp_get_attachment_image($logo, 'sponsor-thumb-size', '', ["class" => "sponsor-thumb-size"]);
        $sponsor_mobile_size = wp_get_attachment_image($logo, 'sponsor-thumb-mobile-size', '', ["class" => "sponsor-thumb-mobile-size"]);
        ?>
    <div class="directory-logo">
        <?php if (!empty($main_link)) { ?>
            <a href="<?php echo $main_link?>">
        <?php } ?>
        <?php echo $sponsor_size ?>
        <?php echo $sponsor_mobile_size ?>
        <?php if (!empty($main_link)) { ?>
            </a>
        <?php } ?>   
    </div>
    <?php } ?>
</div>
