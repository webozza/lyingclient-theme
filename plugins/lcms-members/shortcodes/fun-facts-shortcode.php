<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * FUN FACTS - shortcode
 */


// FUN FACTS: function that runs when shortcode is called
add_shortcode('dbase_fun_facts', 'dbase_fun_facts_shortcode'); 
function dbase_fun_facts_shortcode( $atts ) {
	// FUN BUSINESS FACTS SECTION
	$facts = array(
		__("Cereal is the second-largest advertiser on television today, behind automobiles.", 'd-base-geodirectory-custom-functions'),
		__("Google was originally called BackRub.", 'd-base-geodirectory-custom-functions'),
		__("Everything you say to Siri is sent to Apple, analyzed, and stored.", 'd-base-geodirectory-custom-functions'),
		__("The most productive day of the workweek is Tuesday.", 'd-base-geodirectory-custom-functions'),
		__("YouTube broadcasts about one-third of the U.S. multimedia entertainment.", 'd-base-geodirectory-custom-functions'),
		__("The world's 100 richest people earned enough money in 2012 to end global poverty four times over.", 'd-base-geodirectory-custom-functions'),
		__("The average smartphone user checks Facebook 14 times a day.", 'd-base-geodirectory-custom-functions'),
		__("More than 80 million 'mouse ears' have been sold at Walt Disney World to date.", 'd-base-geodirectory-custom-functions'),
		__("Seventy percent of small businesses are owned and operated by a single person.", 'd-base-geodirectory-custom-functions'),
		__("Sixty-four percent of consumers have made a purchase decision based on social media content.", 'd-base-geodirectory-custom-functions')
	);

	// get the random fact
	$random_facts_array = array();
	for ($n = 0; $n <= 3; $n++) {

		$facts_random = rand( 0, (count($facts)-1) );

		array_push($random_facts_array, $facts[$facts_random]);

		unset($facts[$facts_random]);	// remove fact from facts array so it is picked duplicate

		$facts = array_values($facts);	// reset facts array index after unset
	}
	//var_dump($random_facts_array);


	// display a random quote
    $html = '';
	$html .= '
			<div style="margin: 50px 20px 20px;">

				<table width="100%" style="border-width: thin thin thin thin; border-style: solid solid solid solid;">
					<thead>
						<tr>
							<th colspan="4" width="25%"><center><em><span style="font-weight: 500;font-size: 1.2rem;color:#ed3439;">'.__('Fun Business Facts','d-base-geodirectory-custom-functions').'</span></em></center></th>
						</tr>
					</thead>
					<tbody>';
	$html .= '			<tr>';
						foreach ( $random_facts_array as $random_fact ) {
	$html .= '				<td width="25%"><div style="text-align: left;line-height:1.3rem;padding:5px;"><em>'.$random_fact.'</em></div></td>';
						}
	$html .= '			</tr>';

	$html .= '				</tbody>';
	/*$html .= '			<tfoot>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
					</tfoot>'; */
	$html .= '	</table>

			</div>
			';

    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
}