<?php
/**
 * Plugin Name: Event Lead Time Notice
 * Description: Displays a warning if an event is starting within 14 days.
 * Version: 1.1
 * Author: Vic (with help from ChatGpt4)
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

 add_action('admin_notices', 'display_event_warning');

function display_event_warning() {

    global $post;

    // Ensure the post is an event and has a start date.
    if(get_post_type($post) != 'tribe_events' || !metadata_exists('post', $post->ID, '_EventStartDate')) {
        return;
    }
    
    // Get the event's start date
    $event_start_date = get_post_meta($post->ID, '_EventStartDate', true);

    // Convert the date to timestamp.
    $event_timestamp = strtotime($event_start_date);
    $current_timestamp = time();

    // Calculate the difference in days
    $lead_days = ceil(($event_timestamp - $current_timestamp) / DAY_IN_SECONDS);


    // Display warning if the event start date is within 14 days
    if ($lead_days < 0) {
        $days_ago = -$lead_days;
        ?>
        <div class="notice notice-warning">
            <p><?php _e("Notice: This event is scheduled to start $days_ago day(s) ago.", 'event_countdown_warning'); ?></p>
        </div>
        <?php
    } elseif ($lead_days == 0) {
        ?>
        <div class="notice notice-warning">
            <p><?php _e("Notice: This event is scheduled to start today.", 'event_countdown_warning'); ?></p>
        </div>
        <?php
     } elseif ($lead_days < 14) {
        $formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);
        $lead_fraction = $lead_days / 14.0;
        $lead_percent_str = $formatter->format($lead_fraction);
        print $formatter->format(.45);

        ?>
        <div class="notice notice-warning">
            <p><?php _e("Notice: This event is scheduled to start in $lead_days day(s), for a $lead_percent_str earnings factor.", 'event_countdown_warning'); ?></p>
        </div>
        <?php
    }
}
