<?php

/**
 * @package shopkeeper3
 * @subpackage processors.element.tv.renders.mgr.output
 */

class modTemplateVarOutputRenderSHKCheckbox extends modTemplateVarOutputRender {
    public function process($value,array $params = array()) {
        
        $lines = $this->tv->parseInput($value, '||', 'array');
        $otag = !empty($params['wraptag']) ? "<".$params['wraptag'].">" : "";
        $ctag = !empty($params['wraptag']) ? "</".$params['wraptag'].">" : "";
        $cssclass = !empty($params['cssclass']) ? $params['cssclass'] : "shk_param";
        if(!isset($params['id'])) $params['id'] = '[[+id]]';
        $o = '';
        $cnt = 0;
        $s_options = '';
        foreach ($lines as $line) {
            if (is_array($line) || strlen($line)==0) continue;
            list($item,$itemvalue) = strpos($line,'==')!==false ? explode("==",$line) : array($line,'');
            $selected = $cnt==0 && !empty($params['first_selected']) ? ' checked="checked"' : '';
            $o .= "\n".$otag.'<input class="'.$cssclass.'" type="checkbox" name="'.$params['param_name'].'__'.$params['id'].'__'.$cnt.'" value="'.$cnt.'__'.$itemvalue.'" id="'.$params['param_name'].$params['id'].$cnt.'"'.$selected.' onclick="'.$params['function'].'" /> <label for="'.$params['param_name'].$params['id'].$cnt.'">'.$item.'</label>'.$ctag;
            $cnt++;
        }

        return $o;

    }
}
return 'modTemplateVarOutputRenderSHKCheckbox';