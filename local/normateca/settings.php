<?php
/**
 * @package  local_normateca
 * @copyright 2023, Luis Felipe Alcocer  <luisfelipealcocersosa@gmail.com>
 */

defined('MOODLE_INTERNAL') || die;
if ( $hassiteconfig ) {
    $settings = new admin_settingpage('local_normateca',get_string('plugintitle','local_normateca'));
    // Create
    $ADMIN->add('localplugins', $settings);

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_configtext('local_normateca/categoryiddb', get_string('categoryiddb', 'local_normateca'),
            get_string('categoryiddbinfo', 'local_normateca'), '', PARAM_RAW, 30));
        $settings->add(new admin_setting_configtext('local_normateca/roleshowmdoule', get_string('roleshowmdoule', 'local_normateca'),
            get_string('roleshowmdouleinfo', 'local_normateca'), '', PARAM_RAW, 50));
        $settings->add(new admin_setting_configcheckbox('local_normateca/aceptvisiblecourse', get_string('aceptvisiblecourse', 'local_normateca'),
            get_string('aceptvisiblecourseinfo', 'local_normateca'),1));
        $settings->add(new admin_setting_configtext('local_normateca/idusersallprivilegios', get_string('idusersallprivilegios', 'local_normateca'),
            get_string('idusersallprivilegiosinfo', 'local_normateca'), '', PARAM_RAW, 50));
        $settings->add(new admin_setting_description('pagina','Gestionar Normateca','/local/normateca/index.html'));
    }
}