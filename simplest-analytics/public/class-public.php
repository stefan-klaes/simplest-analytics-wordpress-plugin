<?php

/**
 *
 * The admin-specific functionality of the plugin.
 *
 */


class Simplest_Analytics_Public
{

	/** The ID of this plugin */
	private $simplest_analytics;

	/**  The version of this plugin */
	private $version;


	/** Initialize the class and set its properties. */
	public function __construct($simplest_analytics, $version)
	{

		$this->simplest_analytics = $simplest_analytics;
		$this->version = $version;
		add_shortcode('tracked_video', array($this, 'simplest_analytics_tracked_video_function'));

	}


	/**
	 * video shortcode
	 */
	public function simplest_analytics_tracked_video_function($atts)
	{

		$attribute = shortcode_atts(
			array(
				'url' => '',
				'tracking_name' => 'video tracking without name',
				'width' => '',
				'height' => '',
			),
			$atts
		);

		$url = isset($attribute["url"]) ? sanitize_text_field($attribute["url"]) : "";
		$tracking_name = isset($attribute["tracking_name"]) ? sanitize_text_field($attribute["tracking_name"]) : "";
		$width = isset($attribute["width"]) ? sanitize_text_field($attribute["width"]) : "100%";
		$height = isset($attribute["height"]) ? sanitize_text_field($attribute["height"]) : "auto";

		$randmom_id = "simplest_analytics_video_" . rand(1, 99999);

		ob_start();
		?>
		<div id="<?php echo esc_html($randmom_id) ?>">
			<?php
			echo do_shortcode('[video src="' . esc_url($url) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"]');
			?>
			<input type="hidden" class="simplest_analytics_video_sec_1" value="">
			<input type="hidden" class="simplest_analytics_video_percent_25" value="">
			<input type="hidden" class="simplest_analytics_video_percent_50" value="">
			<input type="hidden" class="simplest_analytics_video_percent_75" value="">
			<input type="hidden" class="simplest_analytics_video_percent_100" value="">
			<input type="hidden" class="simplest_analytics_video_trackname" value="<?php echo esc_attr($tracking_name) ?>">
		</div>
		<script>
			jQuery(document).ready(function ($) {
				$("#<?php echo esc_html($randmom_id) ?> video").on(
					"timeupdate",
					function (event) {
						onTrackedVideoFrame<?php echo esc_html($randmom_id) ?>(this.currentTime, this.duration, '<?php echo esc_html($randmom_id) ?>');
					});

				function onTrackedVideoFrame<?php echo esc_html($randmom_id) ?>(currentTime, duration, video_wrapper_id) {
					var allowed = simplest_analytics_is_allowed();
					if (!allowed) {
						return;
					}
					var percentage = Math.floor((currentTime / duration) * 100);
					var one_sec = $("#" + video_wrapper_id + " .simplest_analytics_video_sec_1").val();
					var percent_25 = $("#" + video_wrapper_id + " .simplest_analytics_video_percent_25").val();
					var percent_50 = $("#" + video_wrapper_id + " .simplest_analytics_video_percent_50").val();
					var percent_75 = $("#" + video_wrapper_id + " .simplest_analytics_video_percent_75").val();
					var percent_100 = $("#" + video_wrapper_id + " .simplest_analytics_video_percent_100").val();
					var trackname = $("#" + video_wrapper_id + " .simplest_analytics_video_trackname").val();
					if (currentTime > 1 && one_sec == '') {
						$("#" + video_wrapper_id + " .simplest_analytics_video_sec_1").val(currentTime);
						simplest_analytics_track('video:1sec', trackname);
					}
					if (percentage > 25 && percent_25 == '') {
						$("#" + video_wrapper_id + " .simplest_analytics_video_percent_25").val(percentage);
						simplest_analytics_track('video:25%', trackname);
					}
					if (percentage > 50 && percent_50 == '') {
						$("#" + video_wrapper_id + " .simplest_analytics_video_percent_50").val(percentage);
						simplest_analytics_track('video:50%', trackname);
					}
					if (percentage > 75 && percent_75 == '') {
						$("#" + video_wrapper_id + " .simplest_analytics_video_percent_75").val(percentage);
						simplest_analytics_track('video:75%', trackname);
					}
					if (percentage > 99 && percent_100 == '') {
						$("#" + video_wrapper_id + " .simplest_analytics_video_percent_100").val(percentage);
						simplest_analytics_track('video:100%', trackname);
					}
				}
			});
		</script>
		<?php
		$video_content = ob_get_clean();
		return $video_content;
	}


	/**
	 * save tracking result in database
	 */

	public function simplest_analytics_tracking_action()
	{

		$event = isset($_GET["event"]) ? sanitize_text_field($_GET["event"]) : '';
		$type = isset($_GET["type"]) ? sanitize_text_field($_GET["type"]) : '';
		$current_page = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '';
		$referrer = isset($_GET["ref"]) ? sanitize_text_field($_GET["ref"]) : '';

		// get url parameter from current page
		$params = [];
		if (strpos($current_page, "?") > 0) {
			$split_current_page = explode("?", $current_page);
			if (isset($split_current_page[1]) && $split_current_page[1] !== "") {
				$split_current_page = explode("&", $split_current_page[1]);
				if (sizeof($split_current_page) > 0) {
					foreach ($split_current_page as $url_p) {
						$split_url_p = explode("=", $url_p);
						if (isset($split_url_p[0]) && isset($split_url_p[1])) {
							$params[$split_url_p[0]] = $split_url_p[1];
						}
					}
				}
			}
		}

		$found_paras = [];

		$para_options = get_option('simplest_analytivs_url_para');
		if (isset($para_options)) {
			foreach ($para_options as $para_option => $para_val) {
				if (isset($params[$para_option])) {
					$this_para = $params[$para_option] . '==' . date("Y-m-d-H-i");
					$_SESSION["sa_" . $para_option] = $this_para;
					$found_paras[] = $para_val . '==' . $this_para;
				} else {
					$this_para = "";
				}
			}
		}


		$all_url_paras = "";
		if (sizeof($found_paras) > 0) {
			$all_url_paras = join(",", $found_paras);
		}

		$data = [];
		$data['track_type'] = $type;
		$data['website'] = $current_page;
		$data['referrer'] = $referrer;
		$data['parameters'] = $all_url_paras;
		$data['event_action'] = $event;
		$data['date_year'] = date("Y", current_time('timestamp'));
		$data['date_month'] = date("m", current_time('timestamp'));
		$data['date_week'] = date("w", current_time('timestamp'));
		$data['date_full'] = date("Y-m-d H:i:s", current_time('timestamp'));


		simple_analytics_track_data($data);

		wp_die();

	}



	/**
	 * add js tracking code in footer
	 */
	public function tracking_in_footer()
	{

		$general_options = get_option('simplest_analytivs_general');
		$cookie_name = isset($general_options['cookie_name']) ? sanitize_text_field($general_options['cookie_name']) : '';
		$cookie_value = isset($general_options['cookie_value']) ? sanitize_text_field($general_options['cookie_value']) : '';



		?>
		<script>

			function simplest_analytics_is_allowed() {

				try {

					var required_cookie_name = "<?php echo esc_html($cookie_name) ?>";
					var required_cookie_value = "<?php echo esc_html($cookie_value) ?>";
					if (required_cookie_name === "" && required_cookie_value === "") {
						return true;
					}

					var name = required_cookie_name + "=";
					var decodedCookie = decodeURIComponent(document.cookie);
					var ca = decodedCookie.split(';');
					for (var i = 0; i < ca.length; i++) {
						var c = ca[i];
						while (c.charAt(0) === ' ') {
							c = c.substring(1);
						}
						if (c.indexOf(name) === 0) {
							if (c.substring(name.length, c.length) === required_cookie_value) {
								return true;
							}
						}
					}
					return false;
				} catch (e) {
					return false;
				}

			}

			function simplest_analytics_track(type, event) {
				var allowed = simplest_analytics_is_allowed();
				if (!allowed) {
					return;
				}
				if (event !== "") {
					var event = "&event=" + event;
				}
				var simplest_analytics_url = "<?php echo esc_url(admin_url('admin-ajax.php')) ?>?action=simplest_analytics_tracking_action&type=" + type + event + "&ref=" + document.referrer;
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function () {
					if (this.readyState == 4 && this.status == 200) {
						// Typical action to be performed when the document is ready:
						//alert(xhttp.responseText);
					}

				};
				xhttp.open("GET", simplest_analytics_url, true);
				xhttp.send();
			}
			simplest_analytics_track('pageview', '');


			document.addEventListener("DOMContentLoaded", function () {

				<?php

				$event_options = get_option('simplest_analytivs_events');
				if (isset($event_options) && is_array($event_options)) {
					foreach ($event_options as $key => $val) {
						?>
						var trigger_class = "<?php echo esc_html($val) ?>";
						jQuery(trigger_class).bind('click', function () {
							simplest_analytics_track('event', '<?php echo esc_attr($key) ?>');
							return true;
						});
						<?php
					}
				}
				?>


			});
		</script>
		<?php
	}


	/**
	 * add tracking when order created
	 */
	public function track_data_when_order_created($order_id)
	{

		$order = wc_get_order($order_id);

		$data = [];
		$data['track_type'] = "woocommerce";
		$data['parameters'] = $order->get_total();
		$data['event_action'] = "sale";


		simple_analytics_track_data($data);

	}

	public function tracking_wc_ty_page()
	{

		?>
		<script>
			var allowed = simplest_analytics_is_allowed();
			if (allowed) {

				var wc_simplest_analytics_url = "<?php echo esc_url(admin_url('admin-ajax.php')) ?>?action=simplest_analytics_tracking_action&type=event&event=sale&ref=" + document.referrer;
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function () {
					if (this.readyState == 4 && this.status == 200) {
						// Typical action to be performed when the document is ready:
						//alert(xhttp.responseText);
					}

				};
				xhttp.open("GET", wc_simplest_analytics_url, true);
				xhttp.send();
			}
		</script>
		<?php
	}





} // class END


/*
 * public function to track data and use in custom functions
 */
function simple_analytics_track_data($data)
{

	$current_page = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '';

	// get url parameter from current page
	$params = [];
	if (strpos($current_page, "?") > 0) {
		$split_current_page = explode("?", $current_page);
		if (isset($split_current_page[1]) && $split_current_page[1] !== "") {
			$split_current_page = explode("&", $split_current_page[1]);
			if (sizeof($split_current_page) > 0) {
				foreach ($split_current_page as $url_p) {
					$split_url_p = explode("=", $url_p);
					if (isset($split_url_p[0]) && isset($split_url_p[1])) {
						$params[$split_url_p[0]] = $split_url_p[1];
					}
				}
			}
		}
	}

	$found_paras = [];

	$para_options = get_option('simplest_analytivs_url_para');
	if (isset($para_options)) {
		foreach ($para_options as $para_option => $para_val) {
			if (isset($params[$para_option])) {
				$this_para = $params[$para_option] . '==' . date("Y-m-d-H-i");
				$_SESSION["sa_" . $para_option] = $this_para;
				$found_paras[] = $para_val . '==' . $this_para;
			} else {
				$this_para = "";
			}
		}
	}


	$all_url_paras = "";
	if (sizeof($found_paras) > 0) {
		$all_url_paras = join(",", $found_paras);
	}

	if (!session_id()) {
		session_start();
	}

	$session_id = session_id();

	$insert_data = [];
	$insert_data['track_type'] = "pageview";
	$insert_data['website'] = $current_page;
	$insert_data['referrer'] = "";
	$insert_data['parameters'] = $all_url_paras;
	$insert_data['event_action'] = "";
	$insert_data['session_id'] = $session_id;
	$insert_data['date_year'] = date("Y", current_time('timestamp'));
	$insert_data['date_month'] = date("m", current_time('timestamp'));
	$insert_data['date_week'] = date("w", current_time('timestamp'));
	$insert_data['date_full'] = date("Y-m-d H:i:s", current_time('timestamp'));

	foreach ($insert_data as $key => $val) {
		if (isset($data[$key])) {
			$insert_data[$key] = sanitize_text_field($data[$key]);
		}
	}

	// if 'video:' in $insert_data[track_type] explo :
	if (strpos($insert_data['track_type'], 'video:') !== false) {
		try {
			$split_track_type = explode(":", $insert_data['track_type']);
			$insert_data['track_type'] = 'video';
			$insert_data['parameters'] = $split_track_type[1];
		} catch (Exception $e) {
			// do nothing
		}
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'simplest_analytics';
	$wpdb->insert($table_name, $insert_data);

}