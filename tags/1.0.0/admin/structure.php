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

		for ( $i=0; $i<sizeof($get_parameters); $i++ ) {

			$this_para = $get_parameters[$i];
			$this_label = $get_labels[$i];

			if ( $this_para !== "" ) {
				if ( $this_label == "" ) {
				$this_label = $this_para;
				}
				$save_array[$this_para] = $this_label;
			}
			
		}

		update_option($option_name_paras, $save_array);

	}
	else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_events"])) {

		$get_parameters = isset($_POST["event_name"]) ? array_map('sanitize_title', $_POST["event_name"]) : array();
		$get_labels = isset($_POST["event_trigger"]) ? array_map('sanitize_text_field', $_POST["event_trigger"]) : array();
	
		$save_array = [];

		for ( $i=0; $i<sizeof($get_parameters); $i++ ) {

			$this_para = $get_parameters[$i];
			$this_label = $get_labels[$i];

			if ( $this_para == "" || $this_label == "" ) {
				continue;
			}
			$save_array[$this_para] = $this_label;
			
		}

		update_option($option_name_events, $save_array);
		$tab = "events";

	}

	$options = get_option( $option_name_paras );
	$option_paras = [];
	$option_labels = [];
	if (isset($options) && is_array($options) ) {
		foreach ($options as $key => $val) {
			$option_paras[] = $key;
			$option_labels[] = $val;
		}
	}

	$options = get_option( $option_name_events );
	$option_event_name = [];
	$option_event_trigger = [];
	if ( isset($options) && is_array($options) ) {
		foreach ( $options as $key => $val ) {
			$option_event_name[] = $key;
			$option_event_trigger[] = $val;
		}
	}
	


	?>		
	<div class="wrap" style="max-width:1000px">
		<h2><?php
			echo __( 'Simplest Analytics Settings', 'simplest-analytics' ) ?>
		</h2>

		<nav class="nav-tab-wrapper woo-nav-tab-wrapper" style="margin-bottom:10px;">
			<a id="urlparas" onclick="simplest_analytics_toggle_tabs_by_id(this)" class="nav-tab  <?php echo $tab=="url_paras" ? 'nav-tab-active' : '' ?>">
				<?php echo esc_html__( 'URL parameters', 'simplest-analytics' ) ?>
			</a>
			<a id="events" onclick="simplest_analytics_toggle_tabs_by_id(this)" class="nav-tab <?php echo $tab=="url_paras" ? '' : 'nav-tab-active' ?>">
				<?php echo esc_html__( 'Events', 'simplest-analytics' ) ?>
			</a>
		</nav>

		<!-- tab: url parameters -->
		<div id="tab_urlparas" class="all_tabs" <?php echo $tab=="url_paras" ? '' : ' style="display:none"' ?>>
		<form method="post" action="">
		<table class="settings_table">
		<tr class="head_th">
				<th></th>
				<th><?php echo __( 'URL Parameter', 'simplest-analytics' ) ?></th>
				<th><?php echo __( 'Label for reports', 'simplest-analytics' ) ?></th>
			</tr>
			<tr>
				<th></th>
				<td><?php echo __( 'e.g. set up "campaign" and all traffic with campaign=whatever will be tracked. Use the URL parameters like this: ', 'simplest-analytics' ) ?>
				<br><b>www.your-site.com/?campaign=whatever</b>
				</td>
				<td><?php echo __( 'The label will appear as name shown in the reports and statistics. E.g. if you set up the URL parameter "campaign" and the label "Campaign Name" you will see "Campaign Name" in the reports and statistics.', 'simplest-analytics' ) ?></td>
			</tr>

			<?php
            $max_paras = 5;
			for ( $i=0; $i<$max_paras; $i++ ) {
	            $para = $i + 1;
			?>
			<tr>
				<th><?php echo __( 'Parameter', 'simplest-analytics' ) ?> <?php echo esc_html( $para ) ?></th>
				<td>
					<input type="text" name="parameter[]" value="<?php echo isset($option_paras[$i]) ? esc_html($option_paras[$i]) : "" ?>"/>
					<?php 
					if ( $i == 0 ) {
						?><span class="desc">e.g. "campaign"</span><?php
					}
					?>
				</td>
				<td>
					<input type="text" name="label[]" value="<?php echo isset($option_labels[$i]) ? esc_html($option_labels[$i]) : "" ?>"/>
					<?php 
					if ( $i == 0 ) {
						?><span class="desc">e.g. "Campaign Name"</span><?php
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>



		</table>
		<input type="submit" name="submit_urlparas" style="margin-top:20px;" class="button-primary" value="<?php echo __( 'save url parameters', 'simplest-analytics' ) ?>"/>
		</form>
		</div>
		<!-- END tab: url parameters -->



		

		<!-- tab: events -->
		<div id="tab_events" class="all_tabs" <?php echo $tab=="url_paras" ? ' style="display:none"' : '' ?>>
		<form method="post" action="">
		<table class="settings_table">
		<tr class="head_th">
				<th></th>
				<th><?php echo __( 'Event Name', 'simplest-analytics' ) ?></th>
				<th><?php echo __( 'Click class/id', 'simplest-analytics' ) ?></th>
			</tr>
			<tr>
				<th></th>
				<td><?php echo __( 'Set up a name for the event. E.g. you want to track form submissions name the event "form submission".', 'simplest-analytics' ) ?>
				</td>
				<td>
					<?php echo __( 'Events will track when a user clicks on an element. To know what element to track you can setup the id or class of the elememt. Here are a few examples:', 'simplest-analytics' ) ?>
					<br><br><b>.form-submit-btn</b> = <?php echo __( 'element with class', 'simplest-analytics' ) ?> "form-submit-btn"
					<br><b>#confirm-oder</b> = <?php echo __( 'element with id', 'simplest-analytics' ) ?> "confirm-oder"
					<br><b>.order-form.save-btn</b> = <?php echo __( 'element with the two classes', 'simplest-analytics' ) ?> "order-form" and "save-btn"
					<br><b>.order-details .form .btn</b> = <?php echo __( 'Element with "btn" which is in element with class "form" which is in element with class "order-detail"', 'simplest-analytics' ) ?>
				</td>
			</tr>

			<?php
            $max_paras = 5;
			for ( $i=0; $i<$max_paras; $i++ ) {
	            $para = $i + 1;
			?>
			<tr>
				<th><?php echo __( 'Event', 'simplest-analytics' ) ?> <?php echo esc_html( $para ) ?></th>
				<td>
					<input type="text" name="event_name[]" value="<?php echo isset($option_event_name[$i]) ? esc_html($option_event_name[$i]) : "" ?>"/>
					<?php 
					if ( $i == 0 ) {
						?><span class="desc">e.g. "Form Submission"</span><?php
					}
					?>
				</td>
				<td>
					<input type="text" name="event_trigger[]" value="<?php echo isset($option_event_trigger[$i]) ? esc_html($option_event_trigger[$i]) : "" ?>"/>
					<?php 
					if ( $i == 0 ) {
						?><span class="desc">e.g. ".form-submit-btn"</span><?php
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>



		</table>
		<input type="submit" name="submit_events" style="margin-top:20px;" class="button-primary" value="<?php echo __( 'save events', 'simplest-analytics' ) ?>"/>
		</form>
		</div>
		<!-- END tab: events -->



	</div> 

		