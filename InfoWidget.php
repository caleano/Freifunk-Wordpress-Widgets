<?php namespace Caleano\Freifunk\Widget;

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
            echo $args['before_title']
                . apply_filters('widget_title', $instance['title'])
                . $args['after_title'];
        }

        if (!empty($instance['text'])) {
            echo '<p>'
                . $args['before_text']
                . apply_filters('widget_text', $this->parseOutput($instance['text']))
                . $args['after_text']
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
        // Mails
        $code = preg_replace(
            '/(([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,}))/i',
            '<a href="mailto:${1}">${1}</a>',
            $code
        );

        // URLs
        $code = preg_replace(
            '/(^|\s|\(|\[)(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\d\.-\?&%]+)*($|\s|\)|\])/i',
            '${1}<a href="${2}${3}.${4}${5}" target="_blank">${3}.${4}</a>${6}',
            $code
        );

        // Twitter
        $code = preg_replace(
            '/(?:^|\s)@([\w\d_]+)(?:$|\s)/i',
            '<a href="https://www.twitter.com/${1}">@${1}</a>',
            $code
        );

        // Includes
        $code = preg_replace_callback(
            '/\[include\:(\w+\.txt)\]/i',
            function ($matches) {
                if (empty($matches[1]) || !file_exists($matches[1])) {
                    return '';
                }

                return file_get_contents($matches[1]);
            },
            $code
        );

        // Create Newlines
        $code = nl2br($code);

        return $code;
    }
}
