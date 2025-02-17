<?php
/**
 * Plugin Name: AzuraCast Playing Next WebSocket
 * Description: Connects to AzuraCast WebSocket to fetch the next playing song and display it using a shortcode.
 * Version: 1.0
 * Author: s0t0na
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class AzuraCast_WebSocket {
    private static $instance = null;
    private $azuracast_url;
    private $station_name;
    private $show_album_art;
    private $refresh_interval;

    public function __construct() {
        $this->azuracast_url = get_option('azuracast_url', '');
        $this->station_name = get_option('azuracast_station_name', '');
        $this->show_album_art = get_option('azuracast_show_album_art', 'yes');
        $this->refresh_interval = get_option('azuracast_refresh_interval', '5000');

        add_shortcode('azura_playing_next', [$this, 'display_playing_next']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_admin_menu() {
        add_options_page('AzuraCast WebSocket Settings', 'AzuraCast WebSocket', 'manage_options', 'azuracast-websocket', [$this, 'settings_page']);
    }

    public function register_settings() {
        register_setting('azuracast-websocket-settings', 'azuracast_url');
        register_setting('azuracast-websocket-settings', 'azuracast_station_name');
        register_setting('azuracast-websocket-settings', 'azuracast_show_album_art');
        register_setting('azuracast-websocket-settings', 'azuracast_refresh_interval');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>AzuraCast WebSocket Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('azuracast-websocket-settings'); ?>
                <?php do_settings_sections('azuracast-websocket-settings'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">AzuraCast WebSocket URL</th>
                        <td><input type="text" name="azuracast_url" value="<?php echo esc_attr(get_option('azuracast_url')); ?>" size="50" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Station Name</th>
                        <td><input type="text" name="azuracast_station_name" value="<?php echo esc_attr(get_option('azuracast_station_name')); ?>" size="50" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Show Album Art</th>
                        <td>
                            <select name="azuracast_show_album_art">
                                <option value="yes" <?php selected(get_option('azuracast_show_album_art'), 'yes'); ?>>Yes</option>
                                <option value="no" <?php selected(get_option('azuracast_show_album_art'), 'no'); ?>>No</option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Refresh Interval (ms)</th>
                        <td><input type="number" name="azuracast_refresh_interval" value="<?php echo esc_attr(get_option('azuracast_refresh_interval')); ?>" min="1000" step="1000" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function fetch_playing_next() {
        $ws_url = esc_js($this->azuracast_url);
        $station = esc_js($this->station_name);
        $refresh_interval = intval($this->refresh_interval);
        $show_album_art = esc_js($this->show_album_art) === 'yes' ? 'true' : 'false';

        $sub_message = json_encode(["subs" => ["station:$station" => new stdClass()]]);

        $script = "
            <script>
                let ws;
                function connectWebSocket() {
                    if (ws) ws.close();
                    ws = new WebSocket('wss://$ws_url/api/live/nowplaying/websocket');
                    ws.onopen = function() { ws.send($sub_message); };
                    ws.onmessage = function(event) {
                        let data = JSON.parse(event.data);
                        if (data.pub && data.pub.data && data.pub.data.np) {
                            let playingNext = data.pub.data.np.playing_next;
                            if (playingNext && playingNext.song) {
                                let nextSongHTML = '';
                                if ($show_album_art && playingNext.song.art) {
                                    nextSongHTML += `<img src='${playingNext.song.art}' onerror='this.style.display=\"none\"' style='width:50px; height:50px; margin-right:10px;'>`;
                                }
                                nextSongHTML += playingNext.song.artist + ' - ' + playingNext.song.title;
                                requestAnimationFrame(() => {
                                    document.getElementById('azura-playing-next').innerHTML = nextSongHTML;
                                });
                            }
                        }
                    };
                    ws.onerror = function() { setTimeout(connectWebSocket, $refresh_interval); };
                }
                connectWebSocket();
            </script>
            <div id='azura-playing-next'>Loading...</div>
        ";

        return $script;
    }

    public function display_playing_next() {
        return $this->fetch_playing_next();
    }
}

add_action('plugins_loaded', function() {
    AzuraCast_WebSocket::get_instance();
});
