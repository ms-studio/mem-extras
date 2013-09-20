<?php 

// NOTE: regarding translation:
// http://codex.wordpress.org/I18n_for_WordPress_Developers

// use the textdomain of the MEM plugin: 
// example: __('Event Dates', 'mem')


function mem_date_processing($start_date, $end_date) {

		// 0) Test for the key custom fields.
					
			$start_date_iso = $start_date;
			if (strlen($start_date_iso) > 10) {
					$start_date_iso = substr_replace($start_date_iso, 'T', 10 , 1);
			}
			// replace space with T in iso string
			// 2013-03-11T06:35
			
			// reset our variables
			$event_date = '';
			$event_date_yr = '';
			$start_year = '';
			$end_year = '';
			$event_is_future = false;
			$ndash = '<span class="ndash">–</span>';
			
			// 1) test and define start date values
			
			if ($start_date !== "" ) { 
					if (strlen($start_date) > 5) { // yes = the month is defined
							
							$unix_start = strtotime($start_date);
							$start_year = date_i18n( "Y", $unix_start);
							$start_month = date_i18n( "F", $unix_start);
					
					} else { // no = only the year is defined
					
							$event_date = $start_date;
							$start_year = $event_date;
							
							// NOTE: 
							// $unix_start is not yet defined.
							// let's create a fake Unix_Start:
							$unix_start = strtotime($start_year.'-01-01');
					}
					$event_date_yr = $start_year;
			}
			
			// 2) test and define END date values
		
		if ($end_date !== "" ) { 
				if (strlen($end_date) > 5) { // Yes = the month is defined
						
						$unix_end = strtotime($end_date);
						$end_year = date_i18n( "Y", $unix_end);
						$end_month = date_i18n( "F", $unix_end);
				
				} else { // No = only the year is defined
				
						$end_year = $end_date;
						// let's create a fake Unix_End:
						$unix_end = strtotime($end_year.'-01-01');
				}
				
				if ($end_year != $start_year ) {
					$event_date_yr .= $ndash . $end_year;
				}
		}
			
			// 3) process the values
			
			if ($start_date !== "" ) {
					
				// first: test if we have more than 5 chars
				// *************************************************
				
				if (strlen($start_date) > 5) { // MONTH is defined
							
						if ($end_date !== "" ) {
						
							// 1) YES, we have START and END date.
							// ********************************
												
							// 2) test if start/end occurs in the same year.
							// **********************************************
							
							if ($start_year == $end_year) { // YES, same year!
								
								// 3) test if start/end occurs the same month...
								// ********************************
								
								if ($start_month == $end_month) { // YES, same month!
			
									// 4) test if start/end occurs the same day
									// ********************************
									
									$start_day = date_i18n( "j", $unix_start);
									$end_day = date_i18n( "j", $unix_end);
									
									$event_date_short = date_i18n( "F Y", $unix_start);
									
									if ($start_day == $end_day) { // yes, same day! 
									
											// 5) the events must have a different time
											// *****************************************
											
												$event_date = date_i18n( "l j F Y, H\hi", $unix_start); // mardi 3 janvier 2012
												$event_date .= $ndash . date_i18n( "H\hi", $unix_end);
									
									} else { // two different days, but same month.
										
												if ( (date_i18n( "j", $unix_start)) == 1) { // 1er
												  $event_date = date_i18n( "\D\u j\e\\r", $unix_start);
												} else { // sinon
												  $event_date .= date_i18n( "\D\u j", $unix_start); // Du 3 ...	
												}
												
												if ( (date_i18n( "j", $unix_end)) == 1) { // 1er
												  $event_date = date_i18n( " \a\u j\e\\r F Y", $unix_end);
												} else { // sinon
												  $event_date .= date_i18n( " \a\u j F Y", $unix_end);	
												} // au 17 mars 2012
									
									}
									
								} else { // two different months, but same year
								
									$event_date_short = date_i18n( "F", $unix_start); // janvier
									$event_date_short .= $ndash . date_i18n( "F Y", $unix_end); // - mars 2012
								
								// TEST if the DAY is definded
									if (strlen($start_date) > 7)  {
									
											if ( (date_i18n( "j", $unix_start)) == 1) { // 1er
											  $event_date = date_i18n( _x( 'F jS', 'First day of month', 'mem' ), $unix_start);
											} else { // sinon
											  $event_date = date_i18n( _x( 'F jS', 'Other day of month', 'mem' ), $unix_start);	
											}
											
											if ( (date_i18n( "j", $unix_end)) == 1) { // 1er
											  $event_date .= date_i18n( _x( ' – F jS Y', 'First day of month', 'mem' ), $unix_end);
											} else { // sinon
											  $event_date .= date_i18n( _x( ' – F jS Y', 'Other day of month', 'mem' ), $unix_end);	
											}
										
									} else {
											// DAY not defined = output only the month.
											$event_date = $event_date_short;
									}
									
								} // END month testing
								
								
							} else { // two different years
							
										$event_date_short = date_i18n( "F Y", $unix_start); // janvier 2010 ...
										$event_date_short .= $ndash . date_i18n( "F Y", $unix_end); // mars 2012
								
								if (strlen($start_date) > 7) { // DAY is defined
										$event_date = date_i18n( "\D\u j F Y", $unix_start); // 3 janvier 2010 ...
										$event_date .= date_i18n( " \a\u j F Y", $unix_end); // 17 mars 2012
								} else { // DAY not defined
										$event_date = $event_date_short;
								}
							
							} // END year testing
							
						} else {
						
						// we have ONLY a START date.
						// ********************************
						
								$event_date_short = date_i18n( "F Y", $unix_start); // janvier 2010
						
						// 1) test if DAY is defined.
							
								if (strlen($start_date) > 7) { // DAY is defined.					
								
								// 2) test if TIME is defined.
								
									if (strlen($start_date) > 11) { // TIME is defined.
											
												if ( (date_i18n( "j", $unix_start)) == 1) { // 1st day of month ?
												  $event_date = date_i18n( _x( 'l F jS Y, g:i a', 'First day of month', 'mem' ), $unix_start);
												} else {
												  $event_date = date_i18n( _x( 'l F jS Y, g:i a', 'Other day of month', 'mem' ), $unix_start);	
												}
											
										} else { // TIME is not defined.
										
												if ( (date_i18n( "j", $unix_start)) == 1) { // 1st day of month ?
												  $event_date = date_i18n( _x('l, F jS Y', 'First day of month', 'mem'), $unix_start);
												} else {
												  $event_date = date_i18n( _x('l, F jS Y', 'Other day of month', 'mem'), $unix_start);
												}
										} // end testing for TIME.
									
								} else { // DAY not defined.
								
									$event_date = $event_date_short;
								
								} // end DAY testing.
								
							} // end of END date testing.
						
					} else  { // YEAR only is defined
					
						// For YEAR ONLY display: 
						// Test if we should show the END year.
						
						if ($end_date !== "" ) { 
								if ($end_year != $start_year ) {
									$event_date .= $ndash . $end_year ;
								}
						}
						
						$event_date_short = $event_date;
										
				} // end Process: year only
					
		} // end Process.
		
		if ( $mem_unix_now <= $unix_start ) {
			$event_is_future = true;
		}
		
			// build an ARRAY to return:
			
			$event_date_array = array(
			    "date" => $event_date, // Jeudi 19 septembre 2013
			    "date-short" => $event_date_short,
			    "start-iso" => $start_date_iso,
			    "start-unix" => $unix_start,
			    "date-year" => $event_date_yr, // can be 2012-2013
			    "start-year" => $start_year,
			    "end-year" => $end_year,
			    "is-future" => $event_is_future // true or false
			);
			
			return $event_date_array;

} // end of function

      
?>