<?php

class bf3infobox extends WP_Widget
{
    public function __construct()
    {
        $params = array(
            'description' => 'Display a widget with your Battlefield 3 (BF3) stats',
            'name'        => 'BF3 Player Infobox'
        );
        parent::__construct('bf3infobox','',$params);
    }

    public function form($instance)
    {
        extract($instance);
        if(!isset($platform)) $platform = 'pc';
        include(dirname( __FILE__ ) .'/../views/form.phtml');
    }

    /**
     * Clear player cache and reload stats from bf3stats.com on save action
     * @param array $newInstance
     * @param array $oldInstance
     * @return array
     */
    public function update($newInstance, $oldInstance)
    {
        $api = new bf3stats_api();
        $api->clearPlayerCache($newInstance['playername']);
        $api->getPlayerStats($newInstance['playername'], $newInstance['platform']);
        return $newInstance;
    }

    public function widget($args, $instance)
    {
        extract($args);
        extract($instance);
        $options = get_option('bf3infobox_settings');

        if(!isset($platform)) $platform = 'pc';

        $api = new bf3stats_api();
        $data = $api->getPlayerStats($instance['playername'],$platform);

        if(!isset($title))    $title    = 'BF3 Infobox';

        $title = apply_filters('widget_title', $title);
        include(dirname( __FILE__ ) .'/../views/infobox.phtml');
    }


}

