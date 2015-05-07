<?php

/**
 * code for user facing transformation-911.php page
 */
function getPracticeDetails_new() {
    global $wpdb;
    require_once(ABSPATH . 'wp-load.php');
 /*   $query = "SELECT * FROM  `sadhanas`  where sadhana_type_id='1'";
    $result = $wpdb->query($query);*/
    /* var_dump($result); */
    $data = "";
    $i = 0;
  /*  $result = $wpdb->get_results($query, OBJECT);
    $result = (array) $result;*/
    //var_dump($result);
    $data = get_all_common_recommended_practices_mongo(1);
 /*   $i = 0;
    foreach ($result as $row) {
        $data[$i] = array($row->sadhana_name, $row->sadhana_description, $row->image_rel_path, $row->page_id);
        //var_dump($data[$i]);
        $i += 1;
    }*/

    /* free result set */
    // $result->free();

    /* close connection */
    return $data;
}

function displayPractice_new() {
    echo "<table class=\"table\">";
    //echo "<thead><tr>";
    //echo "<th></th>";
    // echo "<th>Practice</th>";
    //echo "<th>Description</th>";
    // echo "<th></th>";
    //echo "</tr></thead>";
    echo "<tbody>";

    $customers = getPracticeDetails_new();
    foreach ($customers as $customer) {
        $imgfile = "saiwalk-with-values/images/" . $customer[2];
        echo "<tr>";
        echo "<td>";
        echo '<img src="' . plugins_url($imgfile, dirname(__FILE__)) . '"  class=\"img-responsive\"   width=\"35\" height=\"35\" > ';
        echo "</td>";
        echo "<td style=\"width:75%;\"> <strong> " . $customer[0] . "</strong></br><div class=\"span4\"> " . $customer[1] . "<a  href=\"?page_id=" . $customer[3] . "\" >...... Learn More<a></div></td>";
        // echo "<td><a class=\"btn btn-primary btn-small\" href=\"?page_id=".$customer[2]."\" >Learn More<a> </td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}

function transformation_911_content_new() {

    /* if (is_user_logged_in()): */
    echo "<div class=\"pull-left\"> <a class=\"btn btn-success btn-lg\" href=\"?page_id=285\">Practice Signup</a><br/></div><br/>";
    ?>
    
   <ul class="nav nav-tabs">
        <li class="active"><a href="#commonpractices"><h5>9 - Common Practices</h5></a></li>
        <li class="HealthyBody_tab"><a href="#HealthyBody"><h5>1- Healthy Body</h5></a></li>
        <li class="HealthyMind_tab"><a href="#HealthyMind"><h5>1- Healthy Mind</h5></a></li>
    </ul>
    <div class="tab-content">
        <div id="commonpractices" class="tab-pane active scroll_tab">
    <?php displayPractice_new(); ?>
        </div>



        <div id="HealthyBody" class="tab-pane">
            <h2>Healthy Body</h2>
            <p>The aim of this practice is to come up with a "Healthy Body" goal that needs more than 21days to accomplish and the one that you care about most. Here is a sample plan:<br/>
                Goal: Do a 10k in 3 months time<br/>
                sub-goals:
            <ol>
                <li>Eat a salad for a meal 3 times a week</li>
                <li>Reduce intake of sweets</li>
                <li> Reduce restaurant eating</li>
                <li>Individual workout 4 days/week</li>
                <li>Group/Buddy walk once a week</li>
            </ol>

            Some more potential ideas:<br/>
            <ol>
                <li>Half Marathon/5k/10k</li/>
                <li>Reduce weight to x pounds</li/>
                <li>Reduce cholesterol level</li/>
                <li>Reduce restaurant eating</li/>
                <li>consistent sleep and wake up times</li/>
                <li>improve level of fitness, flexibility</li/>
                <li>Minimize intake of processed foods</li/>
                <li>Include raw fruits/salads/smoothie made from scratch and cook fresh.</li/>
                <li>To stop added ingredients and know what goes on my plate.</li/>
                <li>Maintain 4-5 mile walk each day</li/>
            </ol>
        </div>

        <div  id="HealthyMind" class="tab-pane">
            <h2>Healthy Mind Goal</h2>
            <p>Again, make this one specific to "Healthy Mind" and here is a sample plan:<br/>
                Goal: peace and harmony at home<br/>
                sub-goals:<br/>
            <ol><li>Reduce arguments with spouse</li>
                <li>Be aware of the words to speak</li>
                <li>Respond, not react</li>
                <li>Consider the source of other viewpoint</li>
                <li>write a journal</li>
                <li>submit frustrations to swami</li>
            </ol>

            Some more potential ideas:<br/>
            <ol>
                <li>Respond instead of reacting, practice with situations that arise with home members</li>
                <li>Likhita japam</li>
                <li>Spend quality time with family </li>
            </ol>
            </p>
        </div>
        <script>
            $(document).ready(function () {
                var hash = window.location.hash;
                var link = $('a');
                $('.nav-tabs li a').click(function (e) {
                    e.preventDefault();
                    $(this).tab('show');
                    $('.tab-content > .tab-pane.active').jScrollPane();
                });
            })
        </script>
    <?php
}

// Register a new shortcode: [swwv_911_transformation_new]
add_shortcode('swwv_911_transformation_new', 'transformation_911_new');

// The callback function that will replace [book]
function transformation_911_new() {
    ob_start();
    transformation_911_content_new();
    return ob_get_clean();
}
?>