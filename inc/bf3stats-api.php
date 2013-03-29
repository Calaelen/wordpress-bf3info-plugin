<?php
/**
 * Calls bf3stats.com API
 * @author Calaelen <info@calaelen.com>
 */
class bf3stats_api
{
    protected $errormsg = '';

    /**
     * Returns Player Array or false
     * @param string $playername Origin ID
     * @param string $platform pc, ps3, 360
     * @return bool|array
     */
    public function getPlayerStats($playername, $platform)
    {
        if(!$playername = $this->sanitizePlayername($playername)) return false;

        //get Player from transient WP Cache
        if ($data = $this->loadPlayerCache($playername)) return $data;

        //no cache? get it from api
        $postdata=array();
        $postdata['player'] = $playername;
        $postdata['opt']= 'all';

        // Run POST Request via CURL - more infos: http://bf3stats.com/api
        $c=curl_init('http://api.bf3stats.com/'.$platform.'/player/');
        curl_setopt($c,CURLOPT_HEADER,false);
        curl_setopt($c,CURLOPT_POST,true);
        curl_setopt($c,CURLOPT_USERAGENT,'BF3StatsAPI/0.1');
        curl_setopt($c,CURLOPT_HTTPHEADER,array('Expect:'));
        curl_setopt($c,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($c,CURLOPT_POSTFIELDS,$postdata);
        $data=curl_exec($c);
        $statuscode=curl_getinfo($c,CURLINFO_HTTP_CODE);
        curl_close($c);

        if($statuscode==200) {
            $data=json_decode($data,true);
            if($data['status'] != 'data') {
                $this->errormsg = $data['status'];
                return false;
            }
            $this->errormsg = '';
            $this->savePlayerCache($playername, $data);
            return $data;
        } else {
            $this->errormsg = "BF3 Stats API error status: ".$statuscode;
            return false;
        }


    }


    /** @return string */
    public function getErrorMsg() {
        return $this->errormsg;
    }

    /**
     * WordPress Transient Cache Loader
     * @param string $playername
     * @return array|bool
     */
    public function loadPlayerCache($playername) {
        $data = false;
        if($playername = $this->sanitizePlayername($playername)) {
            $data = get_transient( 'bf3_stats_playerdata_'.$playername );
        }
        return $data;
    }

    /**
     * WordPress Transient Cache Save
     * @param string $playername
     * @param array $data
     */
    public function savePlayerCache($playername, $data) {
        if($playername = $this->sanitizePlayername($playername) AND is_array($data)) {
            set_transient( 'bf3_stats_playerdata_'.$playername, $data, 1 * HOUR_IN_SECONDS ); //cache one hour
        }
    }

    /**
     * WordPress Transient Cache Delete
     * @param string $playername
     */
    public function clearPlayerCache($playername) {
        if($playername = $this->sanitizePlayername($playername)) {
            delete_transient( 'bf3_stats_playerdata_'.$playername );
        }
    }

    /**
     * @param string $playername
     * @return bool|string
     */
    protected function sanitizePlayername($playername) {
        $playername = trim(strtolower(esc_attr($playername)));
        if(!$playername) {
            $this->errormsg = "No valid playername";
            return false;
        } else {
            return $playername;
        }
    }

}