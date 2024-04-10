<?php
namespace block_normateca_unadm\event;

defined('MOODLE_INTERNAL') || die();

class consult_normateca_unadm extends \core\event\base {

    protected function init() {
//        $this->data['objecttable'] = 'block_normateca_unadm';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('consult_normateca_unadm', 'block_normateca_unadm');
    }

    public function get_description() {
        if (isset($this->other['filtro']) && isset($this->other['busqueda'])){
            return "El usuario con el id '{$this->userid}' realizó la busqueda con los parámetros, filtro '{$this->other['filtro']}' con la busqueda '{$this->other['busqueda']}'";
        }
    }
}

