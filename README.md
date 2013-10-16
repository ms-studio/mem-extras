mem-extras
==========

Useful functions for the MEM WordPress plugin.

Adds a mem_date_processing($start,$end) function, with start and end date arguments.

Usage example:

    $mem_date = mem_date_processing( 
        get_post_meta($post->ID, '_mem_start_date', true) , 
        get_post_meta($post->ID, '_mem_end_date', true)
    );

We now have a $mem_date array, which contains lots of useful information.

We then can output the date with 

    echo $archive_array[$key]["date-short"];
    
## NOTE ##

Call the mem_date_processing function by putting an include into the main `functions.php` file:

    require_once('mem-extras/date-function.php');