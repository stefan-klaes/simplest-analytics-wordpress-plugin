<?php
/**
 * Admin Settings Page
 */


$option_name_paras = 'simplest_analytivs_url_para';
$option_name_events = 'simplest_analytivs_events';
$tab = "url_paras";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_urlparas"])) {

	$get_parameters = isset($_POST["parameter"]) ? array_map('sanitize_title', $_POST["parameter"]) : array();
	$get_labels = isset($_POST["label"]) ? array_map('sanitize_text_field', $_POST["label"]) : array();

	$save_array = [];
	$unique_names = [];

	for ($i = 0; $i < sizeof($get_parameters); $i++) {

		$this_para = $get_parameters[$i];
		$this_label = $get_labels[$i];

		if ($this_para !== "") {
			if ($this_label == "") {
				$this_label = $this_para;
			}
			if (in_array($this_para, $unique_names)) {
				$this_para = $this_para . "_" . substr(md5(rand()), 0, 5);
			}
			$save_array[$this_para] = $this_label;
			$unique_names[] = $this_para;
		}

	}


	update_option($option_name_paras, $save_array);

} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_events"])) {

	$get_parameters = isset($_POST["event_name"]) ? array_map('sanitize_title', $_POST["event_name"]) : array();
	$get_labels = isset($_POST["event_trigger"]) ? array_map('sanitize_text_field', $_POST["event_trigger"]) : array();

	$save_array = [];
	$unique_names = [];

	for ($i = 0; $i < sizeof($get_parameters); $i++) {

		$this_para = $get_parameters[$i];
		$this_label = $get_labels[$i];

		if ($this_para == "" || $this_label == "") {
			continue;
		}
		if (in_array($this_para, $unique_names)) {
			$this_para = $this_para . "_" . substr(md5(rand()), 0, 5);
		}
		$save_array[$this_para] = $this_label;
		$unique_names[] = $this_para;

	}

	update_option($option_name_events, $save_array);
	$tab = "events";

}

$options = get_option($option_name_paras);
$option_paras = [];
$option_labels = [];
if (isset($options) && is_array($options)) {
	foreach ($options as $key => $val) {
		$option_paras[] = $key;
		$option_labels[] = $val;
	}
}

$options = get_option($option_name_events);
$option_event_name = [];
$option_event_trigger = [];
if (isset($options) && is_array($options)) {
	foreach ($options as $key => $val) {
		$option_event_name[] = $key;
		$option_event_trigger[] = $val;
	}
}



?>
<div class="wrap" style="max-width:1000px">
	<h2>
		<?php
		echo __('Simplest Analytics Settings', 'simplest-analytics') ?>
	</h2>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper" style="margin-bottom:10px;">
		<a id="urlparas" onclick="simplest_analytics_toggle_tabs_by_id(this)"
			class="nav-tab  <?php echo $tab == "url_paras" ? 'nav-tab-active' : '' ?>">
			<?php echo esc_html__('URL parameters', 'simplest-analytics') ?>
		</a>
		<a id="events" onclick="simplest_analytics_toggle_tabs_by_id(this)"
			class="nav-tab <?php echo $tab == "events" ? 'nav-tab-active' : '' ?>">
			<?php echo esc_html__('Events', 'simplest-analytics') ?>
		</a>
		<a id="videos" onclick="simplest_analytics_toggle_tabs_by_id(this)"
			class="nav-tab <?php echo $tab == "videos" ? 'nav-tab-active' : '' ?>">
			<?php echo esc_html__('Videos', 'simplest-analytics') ?>
		</a>
		<a id="database" onclick="simplest_analytics_toggle_tabs_by_id(this)"
			class="nav-tab <?php echo $tab == "database" ? 'nav-tab-active' : '' ?>">
			<?php echo esc_html__('Database', 'simplest-analytics') ?>
		</a>
	</nav>

	<!-- tab: url parameters -->
	<div id="tab_urlparas" class="all_tabs" <?php echo $tab == "url_paras" ? '' : ' style="display:none"' ?>>
		<form method="post" action="" class="simplest_analytics_form">
			<table class="settings_table">
				<tr class="head_th">
					<th></th>
					<th>
						<?php echo __('URL Parameter', 'simplest-analytics') ?>
					</th>
					<th>
						<?php echo __('Label for reports', 'simplest-analytics') ?>
					</th>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php echo __('e.g. set up "campaign" and all traffic with campaign=whatever will be tracked. Use the URL parameters like this: ', 'simplest-analytics') ?>
						<br><b>www.your-site.com/?campaign=whatever</b>
					</td>
					<td>
						<?php echo __('The label will appear as name shown in the reports and statistics. E.g. if you set up the URL parameter "campaign" and the label "Campaign Name" you will see "Campaign Name" in the reports and statistics.', 'simplest-analytics') ?>
					</td>
				</tr>

				<?php
				$max_paras = 5;
				if (sizeof($option_paras) > $max_paras) {
					$max_paras = sizeof($option_paras);
				}
				for ($i = 0; $i < $max_paras; $i++) {
					$para = $i + 1;
					?>
					<tr>
						<th>
							<?php echo __('Parameter', 'simplest-analytics') ?>
							<?php echo esc_html($para) ?>
						</th>
						<td>
							<input type="text" name="parameter[]" class="simplest_analytics_check_duplicate"
								value="<?php echo isset($option_paras[$i]) ? esc_html($option_paras[$i]) : "" ?>" />
							<?php
							if ($i == 0) {
								?><span class="desc">e.g. "campaign"</span>
								<?php
							}
							?>
						</td>
						<td>
							<input type="text" name="label[]"
								value="<?php echo isset($option_labels[$i]) ? esc_html($option_labels[$i]) : "" ?>" />
							<?php
							if ($i == 0) {
								?><span class="desc">e.g. "Campaign Name"</span>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>

				<tr>
					<th></th>
					<td colspan="2">
						<div class="button button-primary" onclick="simplest_analytics_add_more('url')">
							<?php echo __('add more', 'simplest-analytics') ?>
						</div>
					</td>
				</tr>


			</table>
			<input type="submit" name="submit_urlparas" style="margin-top:20px;" class="button-primary"
				value="<?php echo __('save url parameters', 'simplest-analytics') ?>" />
		</form>
	</div>
	<!-- END tab: url parameters -->





	<!-- tab: events -->
	<div id="tab_events" class="all_tabs" <?php echo $tab == "events" ? '' : ' style="display:none"' ?>>
		<form method="post" action="" class="simplest_analytics_form">
			<table class="settings_table">
				<tr class="head_th">
					<th></th>
					<th>
						<?php echo __('Event Name', 'simplest-analytics') ?>
					</th>
					<th>
						<?php echo __('Click class/id', 'simplest-analytics') ?>
					</th>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php echo __('Set up a name for the event. E.g. you want to track form submissions name the event "form submission".', 'simplest-analytics') ?>
					</td>
					<td>
						<?php echo __('Events will track when a user clicks on an element. To know what element to track you can setup the id or class of the elememt. Here are a few examples:', 'simplest-analytics') ?>
						<br><br><b>.form-submit-btn</b> =
						<?php echo __('element with class', 'simplest-analytics') ?> "form-submit-btn"
						<br><b>#confirm-oder</b> =
						<?php echo __('element with id', 'simplest-analytics') ?> "confirm-oder"
						<br><b>.order-form.save-btn</b> =
						<?php echo __('element with the two classes', 'simplest-analytics') ?> "order-form" and
						"save-btn"
						<br><b>.order-details .form .btn</b> =
						<?php echo __('Element with "btn" which is in element with class "form" which is in element with class "order-detail"', 'simplest-analytics') ?>
					</td>
				</tr>

				<?php
				$max_paras = 5;
				if (sizeof($option_event_name) > $max_paras) {
					$max_paras = sizeof($option_event_name);
				}
				for ($i = 0; $i < $max_paras; $i++) {
					$para = $i + 1;
					?>
					<tr>
						<th>
							<?php echo __('Event', 'simplest-analytics') ?>
							<?php echo esc_html($para) ?>
						</th>
						<td>
							<input type="text" name="event_name[]" class="simplest_analytics_check_duplicate"
								value="<?php echo isset($option_event_name[$i]) ? esc_html($option_event_name[$i]) : "" ?>" />
							<?php
							if ($i == 0) {
								?><span class="desc">e.g. "Form Submission"</span>
								<?php
							}
							?>
						</td>
						<td>
							<input type="text" name="event_trigger[]"
								value="<?php echo isset($option_event_trigger[$i]) ? esc_html($option_event_trigger[$i]) : "" ?>" />
							<?php
							if ($i == 0) {
								?><span class="desc">e.g. ".form-submit-btn"</span>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>

				<tr>
					<th></th>
					<td colspan="2">
						<div class="button button-primary" onclick="simplest_analytics_add_more('event')">
							<?php echo __('add more', 'simplest-analytics') ?>
						</div>
					</td>
				</tr>

			</table>
			<input type="submit" name="submit_events" style="margin-top:20px;" class="button-primary"
				value="<?php echo __('save events', 'simplest-analytics') ?>" />
		</form>
	</div>
	<!-- END tab: events -->


	<!-- tab: videos -->
	<div id="tab_videos" class="all_tabs" <?php echo $tab == "videos" ? '' : ' style="display:none"' ?>>
		<form method="post" action="">
			<table class="settings_table">
				<tr class="head_th">
					<th></th>
					<th>
						<?php echo __('Track Videos', 'simplest-analytics') ?>
					</th>
					<th>
						<?php echo __('Information', 'simplest-analytics') ?>
					</th>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php echo __('You can track videos by using the shortcode [tracked_video].', 'simplest-analytics') ?>
					</td>
					<td>
						<?php echo __('The shortcode will track when 1sec, 25%, 50%, 75% and 100% of the video is seen. These parameters are tracked once per pageview.', 'simplest-analytics') ?>
					</td>
				</tr>

				<tr>
					<th>
						<?php echo __('Example', 'simplest-analytics') ?>
					</th>
					<td>
						[tracked_video url="https://your-url.com/your-video.mp4" tracking_name="video xyz"]
					</td>
					<td>
						<?php echo __('This will track the video "your-video.mp4" with the name "video xyz".', 'simplest-analytics') ?>
					</td>
				</tr>

				<tr>
					<th>
						<?php echo __('Example', 'simplest-analytics') ?>
					</th>
					<td>
						[tracked_video width="640" url="https://your-url.com/your-video.mp4" tracking_name="video xyz"]
					</td>
					<td>
						<?php echo __('You can also add width and height in the shortcode. Default are the WordPress vidoe shortcode defaults.', 'simplest-analytics') ?>
					</td>
				</tr>


			</table>
	</div>
	<!-- END tab: videos -->


	<!-- tab: database -->
	<?php
	global $wpdb;
	$table_name = $wpdb->prefix . 'simplest_analytics';


	// check if table exists
	$table_exists_query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));

	if (!$wpdb->get_var($table_exists_query) == $table_name) {
		// table not in database. create new table
		require_once(plugin_dir_path(__FILE__) . '../includes/class-activator.php');
		Simplest_Analytics_Activator::activate();
	}


	$sql = $wpdb->prepare("SELECT COUNT(*) AS total_entries FROM $table_name");
	$total_entries = $wpdb->get_var($sql);

	// get values, that are not in current year
	$current_year = date("Y");
	$sql = $wpdb->prepare("SELECT COUNT(*) AS total_entries FROM $table_name WHERE YEAR(date_full) != %d", $current_year);
	$total_entries_not_current_year = $wpdb->get_var($sql);


	?>
	<div id="tab_database" class="all_tabs" <?php echo $tab == "database" ? '' : ' style="display:none"' ?>>
		<form method="post" action="">
			<table class="settings_table">
				<tr class="head_th">
					<th></th>
					<th>
						<?php echo __('value', 'simplest-analytics') ?>
					</th>
					<th>
						<?php echo __('info', 'simplest-analytics') ?>
					</th>
				</tr>

				<tr>
					<th>
						<?php echo __('Database Table name', 'simplest-analytics') ?>
					</th>
					<td>
						<?php echo esc_html($table_name) ?>
					</td>
					<td>
						<?php echo __('All options at this page are only related to this table.', 'simplest-analytics') ?>
					</td>
				</tr>

				<tr>
					<th>
						<?php echo __('Total entries', 'simplest-analytics') ?>
					</th>
					<td>
						<?php echo esc_attr($total_entries) ?>
					</td>
					<td>
						<?php echo __('number of values in the database table', 'simplest-analytics') ?>:
						<?php echo esc_html($table_name) ?>
					</td>
				</tr>

				<tr>
					<th>
						<?php echo __('clear options', 'simplest-analytics') ?>
					</th>
					<td>
						<div id="all" class="button button-primary simplest_analytics_clear_ele">
							<?php echo __('delete all entries', 'simplest-analytics') ?> (
							<?php echo esc_attr($total_entries) ?>)
						</div>
					</td>
					<td>
						<?php echo __('All entries will be removed from the database.', 'simplest-analytics') ?>
					</td>
				</tr>

				<?php
				if ($total_entries_not_current_year > 0) {
					?>
					<tr>
						<th>
							<?php echo __('clear options', 'simplest-analytics') ?>
						</th>
						<td>
							<div id="older_than_year" class="button button-primary simplest_analytics_clear_ele">
								<?php echo __('delete entries older than this year', 'simplest-analytics') ?> (
								<?php echo esc_attr($total_entries_not_current_year) ?>)
							</div>
						</td>
						<td>
							<?php echo __('All entries will be deleted that are not from', 'simplest-analytics') ?>
							<?php echo esc_attr($current_year) ?>.
						</td>
					</tr>
					<?php
				}
				?>

				<tr>
					<th>
						<?php echo __('clear options', 'simplest-analytics') ?>
					</th>
					<td>
						<input type="date" id="delete_before_date"
							style="max-width: 200px;margin-bottom: 10px; display: block" />
						<div id="older_than_date" class="button button-primary simplest_analytics_clear_ele">
							<?php echo __('delete entries older than', 'simplest-analytics') ?>
						</div>
					</td>
					<td>
						<?php echo __('Select a date and all entries that are older than this date will be deleted', 'simplest-analytics') ?>.
					</td>
				</tr>


			</table>
	</div>

	<div id="simplest_analytics_loading" style="display: none">
		<div
			style="background: #000; position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; opacity: 0.5; z-index: 999">
		</div>
		<!-- dentered loading text fixed -->
		<div style="background: #fff; position: fixed; top: 50%; left: 50%; margin-top: -50px; margin-left: -100px; width: 200px; height: 100px; z-index: 1000"
			id="simplest_analytics_loading_text">
			<div style="text-align: center; margin-top: 30px;">
				<?php echo __('loading', 'simplest-analytics') ?>...
			</div>
		</div>
	</div>


	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			// on formsubmit check duplicates
			$('.simplest_analytics_form').submit(function (e) {
				var para = [];
				$('.simplest_analytics_check_duplicate').each(function () {
					var this_val = $(this).val();
					if (this_val !== "") {
						// if not in para push it
						if ($.inArray(this_val, para) !== -1) {
							var alert_txt = '<?php echo esc_html__('Please use unique names. This name is used multiple times:', 'simplest-analytics') ?>';
							alert(alert_txt+' '+this_val);
							e.preventDefault();
							return false;
						}
						para.push(this_val);
					}
				});
			});
			$(".simplest_analytics_clear_ele").click(function (e) {

				// show loading
				$('#simplest_analytics_loading').show();

				var clearType = e.target.id;

				var ajax_url = "<?php echo esc_url(admin_url('admin-ajax.php')) ?>";
				var data = {
					'action': 'simplest_analytics_clear_db',
					'nonce': '<?php echo esc_html(wp_create_nonce('simplest_analytics_clear_nonce')) ?>',
					'clear': clearType,
					'date': jQuery('#delete_before_date').val()
				};

				jQuery.post(ajax_url, data, function (response) {
					// hide loading
					$('#simplest_analytics_loading').hide();
					// if response starts with alert: show
					if (response.indexOf('alert:') == 0) {
						alert(response.replace('alert:', ''));
					}
					else {
						alert(response);
						window.location.reload();
					}
				});

			});

		});
	</script>
	<!-- END tab: database -->



</div>