<?php
/**
 * Created by PhpStorm.
 * User: behme
 * Date: 17.07.2018
 * Time: 14:48
 */

namespace Slim\Helper;


class Debugger
{

    /**
     * @param $data
     */
    public function console($data) {
        $output = $data;
        if ( is_array( $output ) )
            $output = implode( ',', $output);

        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }

}