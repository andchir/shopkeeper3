<?php

/**
 * @package shopkeeper3
 * @subpackage processors.element.tv.renders.mgr.output
 */
class modTemplateVarOutputRenderSHKSelect extends modTemplateVarOutputRender {
    public function process($value,array $params = array()) {
        
        $lines = $this->tv->parseInput($value, '||', 'array');
        $cssclass = !empty($params['cssclass']) ? $params['cssclass'] : "shk_param";
        
        if(!isset($params['id'])) $params['id'] = '[[+id]]';
        $o = '';
        $cnt = 0;
        $s_options = '';
        foreach ($lines as $line) {
            if (strlen($line)==0) continue;
            list($item,$itemvalue) = strpos($line,'==')!==false ? explode("==",$line) : array($line,'');
            $selected = $cnt==0 && !empty($params['first_selected']) ? ' selected="selected"' : '';
            $s_options .= "\n\t".'<option value="'.$cnt.'__'.$itemvalue.'"'.$selected.'>'.$item.'</option>';
            $cnt++;
        }
        
        if(strlen($s_options)>0) $o .= "\n".'<select class="'.$cssclass.'" name="'.$params['param_name'].'__'.$params['id'].'" id="'.$params['param_name'].$params['id'].'" onchange="'.$params['function'].'">'.$s_options."\n".'</select>'."\n";
        
        return $o;
        
    }
}
return 'modTemplateVarOutputRenderSHKSelect';