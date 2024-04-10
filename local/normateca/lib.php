<?php

/**
 * @package  normateca
 * @copyright 2023, Luis Felipe Alcocer  <luisfelipealcocersosa@gmail.com>
 */
require_once(__DIR__.'/../../config.php');
require_once $CFG->libdir.'/formslib.php';

defined('MOODLE_INTERNAL') || die();
function selectCategories(){
    global $DB;
    $html .= "<option value='" . 0 . "'>" . "Selecciona una categoría" . "</option>";
    if(get_config('local_normateca','categoryiddb')!= ''){
        $idCategory = get_config('local_normateca','categoryiddb');
        $categories = $DB->get_records('course_categories',array('id'=> $idCategory),'name');

    }else{
        $categories = $DB->get_records('course_categories',array('visible'=>1),'name');
    }
    foreach ($categories as $category) {
        $html .= "<option value='" . $category->id . "'>" . $category->name . "</option>";
    }
    echo $html;
}
function selectCourses($idcategory,$iduser,$isadmin=0){
    
    $activo = false;
    $defaultCategory = get_config('local_normateca','categoryiddb');
    if($defaultCategory != ''){
        $defaultCategory = get_config('local_normateca','categoryiddb');
    }else{
        $defaultCategory = $idcategory; //TODO agregar a la configuración para cuando solo se tomara en cuenta una categoría
    }
    $allCorusesEnrolUser = [];
    
    if(is_siteadmin($iduser)){
        $admin =1;
    }else {
       
        $admin =0;
        //Valida los cursos a los que pertence.
        $allCorusesEnrol = enrol_get_all_users_courses($iduser,$activo);
        if (sizeof($allCorusesEnrol) > 1) {
            foreach ($allCorusesEnrol as $item) {
                if ($item->category == $defaultCategory) {
                    $context = context_course::instance($item->id);
                    $roles = get_user_roles($context, $iduser);
                    print_object($roles);
                    $rolesAcept = get_config("local_normateca","roleshowmdoule");
                    $rolesAcept = explode('|',$rolesAcept);
                    foreach ($roles as $role) {
                        for($i=0; $i<sizeof($rolesAcept);$i++){
                            if ($role->roleid == $rolesAcept[$i]) {
                                array_push($allCorusesEnrolUser, $item->id);
                            }
                        }
                        
                    }
                }
            }
        } else {
            if ($allCorusesEnrol->category == $defaultCategory) {
                array_push($allCorusesEnrolUser, $allCorusesEnrol->id);
            }
        }
        if($isadmin == 1 ){
            $admin =1 ;
        }
    }
    global $DB;
//    $courses = $DB->get_records('course',array('visible'=>1,'category'=>$idcategory),'fullname');
    $courses = $DB->get_records('course',array('category'=>$idcategory),'fullname');
    $html .= "<option value='" . 0 . "'>" . "Selecciona un curso" . "</option>";
    foreach ($courses as $course){
        if($admin == 0) {
            foreach ($allCorusesEnrolUser as $itemCourse){
                if($itemCourse == $course->id AND $idcategory != 0 ){
                    $html .= "<option value='" . $course->id . "'>" . $course->fullname . "</option>";
                }
            }
        }else{
            if($idcategory != 0){
                $html .= "<option value='" . $course->id . "'>" . $course->fullname . "</option>";
            }
        }
    }
    echo $html;
}
class admin_export_repository_form extends moodleform {
    function definition () {
        $mform = $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $url = new moodle_url('example.csv');
        $link = html_writer::link($url, 'ejemplo.csv');
        $mform->addElement('static', 'examplecsv', get_string('examplecsv', 'local_normateca'), $link);
        $mform->addHelpButton('examplecsv', 'examplecsv', 'local_normateca');

        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

//        $choices = csv_import_reader::get_delimiter_list();
        $choices = array('semicolon'=>';');
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'semicolon');
        }

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        /*$choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'tool_uploaduser'), $choices);
        $mform->setType('previewrows', PARAM_INT);*/

        $choices = array('1'=>'Si', '0'=>'No');
        $mform->addElement('select', 'showdata', get_string('rowpreviewdata', 'local_normateca'), $choices);
        $mform->setType('showdata', PARAM_INT);


        $this->add_action_buttons(false, 'Importar recursos');
//        $mform->addElement('cancel', 'cancel', get_string('cancel'), array('onclick' => 'window.location.href=\'http://www.ejemplo.com\'; return false;'));

    }

    /**
     * Returns list of elements and their default values, to be used in CLI
     *
     * @return array
     */
    public function get_form_for_cli() {
        $elements = array_filter($this->_form->_elements, function($element) {
            return !in_array($element->getName(), ['buttonar', 'userfile', 'previewrows']);
        });
        return [$elements, $this->_form->_defaultValues];
    }
}
class admin_export_repository_form2 extends moodleform {
    function definition () {
        $mform = $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $url = new moodle_url('example.csv');
        $link = html_writer::link($url, 'ejemplo.csv');
        $mform->addElement('static', 'examplecsv', get_string('examplecsv', 'local_normateca'), $link);
        $mform->addHelpButton('examplecsv', 'examplecsv', 'local_normateca');

        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'tool_uploaduser'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'tool_uploaduser'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $choices = array('1'=>'Si', '0'=>'No');
        $mform->addElement('select', 'showdata', get_string('rowpreviewdata', 'local_normateca'), $choices);
        $mform->setType('showdata', PARAM_INT);


        $this->add_action_buttons(true, get_string('uploadusers', 'tool_uploaduser'));
    }

    /**
     * Returns list of elements and their default values, to be used in CLI
     *
     * @return array
     */
    public function get_form_for_cli() {
        $elements = array_filter($this->_form->_elements, function($element) {
            return !in_array($element->getName(), ['buttonar', 'userfile', 'previewrows']);
        });
        return [$elements, $this->_form->_defaultValues];
    }
}
class upload_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'settingsheader', get_string('upload'));
        $mform->addElement('filepicker', 'imagenes', 'Seleccione las imágenes a cargar', null, array('maxbytes' => 0, 'accepted_types' => array('.jpg'), 'multiple' => true));
        $this->add_action_buttons(true, 'Cargar');
    }
}
class simplehtml_form extends moodleform {

    function definition() {

        $mform = $this->_form; // Don't forget the underscore!
        $filemanageropts = $this->_customdata['filemanageropts'];

        // FILE MANAGER
        $mform->addElement('filemanager', 'attachments', 'File Manager Example', null, $filemanageropts);

        // Buttons
        $this->add_action_buttons();
    }
}



function local_filemanager_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    require_login();

    if ($filearea != 'attachment') {
        return false;
    }

    $itemid = (int)array_shift($args);

    if ($itemid != 0) {
        return false;
    }

    $fs = get_file_storage();

    $filename = array_pop($args);
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }

    $file = $fs->get_file($context->id, 'local_filemanager', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!
}


