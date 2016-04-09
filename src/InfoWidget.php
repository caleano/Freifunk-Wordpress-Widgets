<?php namespace Caleano\Freifunk\Widget;

defined('ABSPATH') or die('NOPE');

use Caleano\Freifunk\ContentParser;
use WP_Widget;

/**
 * Class Info
 *
 * @package Caleano\Freifunk\Widget
 * @author  Igor Scheller <igor.scheller@igorshp.de>
 */
class Info extends WP_Widget
{
    /**
     * Sets up the widgets name etc
     */
    public function __construct()
    {
        $widget_ops = array(
            'classname'   => 'Info',
            'description' => 'Informations Widget für Freifunk',
        );
        parent::__construct('freifunk_info', 'Freifunk Info', $widget_ops);
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo ((isset($args['before_title'])) ? $args['before_title'] : '')
                . apply_filters('widget_title', $instance['title'])
                . ((isset($args['after_title'])) ? $args['after_title'] : '');
        }

        if (!empty($instance['text'])) {
            echo '<p>'
                . ((isset($args['before_text'])) ? $args['before_text'] : '')
                . apply_filters('widget_text', $this->parseOutput($instance['text']))
                . ((isset($args['before_text'])) ? $args['after_text'] : '')
                . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     * @return string
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : 'Info';
        $text = !empty($instance['text']) ? $instance['text'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input
                class="widefat"
                id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>"
                type="text"
                value="<?php echo esc_attr($title); ?>">

            <label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Content:'); ?></label>
            <textarea
                class="widefat"
                rows="16"
                cols="20"
                id="<?php echo $this->get_field_id('text'); ?>"
                name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_attr($text); ?></textarea>
        </p>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['text'] = (!empty($new_instance['text'])) ? strip_tags($new_instance['text']) : '';

        return $instance;
    }

    /**
     * Parse the content and create links
     *
     * @param string $code
     * @return string
     */
    private function parseOutput($code)
    {
        $code = ContentParser::parse($code);

        // Create Newlines
        $code = nl2br($code);

        return $code;
    }
}
