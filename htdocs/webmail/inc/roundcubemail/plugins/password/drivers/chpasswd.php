<?php

/**
 * chpasswd Driver
 *
 * Driver that adds functionality to change the systems user password via
 * the 'chpasswd' command.
 *
 * For installation instructions please read the README file.
 *
 * @version 1.0
 * @author Alex Cartwright <acartwright@mutinydesign.co.uk)
 */
 
function password_save($currpass, $newpass)
{
    $cmd = sprintf('echo \'%1$s:%2$s\' | %3$s; echo $?',
                addcslashes($_SESSION['username'], "'"),
                addcslashes($newpass, "'"),
                rcmail::get_instance()->config->get('password_chpasswd_cmd'));

    if (exec($cmd) == 0) {
        return PASSWORD_SUCCESS;
    }
    else {
        raise_error(array(
            'code' => 600,
            'type' => 'php',
            'file' => __FILE__,
            'message' => "Password plugin: Unable to execute $cmd"
            ), true, false);
    }

    return PASSWORD_ERROR;
}

?>
