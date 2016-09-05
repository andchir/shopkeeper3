<?php
/**
 * Form-to-session FormIt hook for Shopkeeper 2.x
 */

/*

Сохранение данных формы в сессию
[[!formIt?
&hooks=`form_to_session`
&preHooks=`form_to_session`
...
]]

-----------------

Добавление данных из другой формы, сохраненных в сессии
[[!formIt?
&hooks=`form_to_session,...`
&preHooks=`form_to_session`
&prependFormData=`1`
&tpl=`FormDataTpl`
...
]]

<table>
    [[+formit.rows_prepend]]
    <tr>
        <td>Name:</td>
        <td>[[+fullname]]</td>
    </tr>
    [[+formit.rows_append]]
</table>

-----------------

Очистка сессий:
[[!form_to_session?
&emptyOn=`12`
]]

*/

//ini_set( 'display_errors', 1 );
//error_reporting(E_ALL);

$output = true;

$submitVar = $modx->getOption('submitVar',$scriptProperties,'submitVar');
$redirectTo = !empty($redirectTo) && is_numeric($redirectTo) ? $redirectTo : 0;
$emptyOn = $modx->getOption('emptyOn',$scriptProperties,'');
$form_id = $modx->getOption('form_id',$scriptProperties,0);
$prependFormData = $modx->getOption('prependFormData',$scriptProperties,'');
$appendFormData = $modx->getOption('appendFormData',$scriptProperties,'');
$emailRowTpl = $modx->getOption('emailRowTpl',$scriptProperties,'formConstruct_emailRowTpl');
$getUserData = $modx->getOption('getUserData',$scriptProperties,'');

$session_id = session_id();
$modx->getService('registry', 'registry.modRegistry');
$modx->registry->addRegister('form_to_session', 'registry.modDbRegister', array('directory' => 'form_to_session'));
$modx->registry->form_to_session->connect();

//Очистка сессии с данными формы
if( empty( $hook ) ){
    
    if( $emptyOn && is_numeric( $emptyOn ) && $modx->resource->id == $emptyOn ){
        
        /*
        $modx->registry->form_to_session->subscribe("/form_data/{$session_id}/");
        $form_data = $modx->registry->form_to_session->read(array('poll_limit' => 1, 'msg_limit' => 1, 'remove_read' => true));
        $modx->registry->removeRegister('form_to_session');
        */
        
        //если удалять, то кэш не очищается, поэтому учищаем знаяние
        $modx->registry->form_to_session->subscribe( "/form_data/" );
        $modx->registry->form_to_session->send(
            "/form_data/",
            array( $session_id => array() ),
            array(
                'ttl' => 86400
            )
        );
        
    }
    
    return '';
    
}

if( !empty( $_POST[$submitVar] ) ){
    
    $modx->registry->form_to_session->subscribe("/form_data/{$session_id}/");
    $form_data = $modx->registry->form_to_session->read(array('poll_limit' => 1, 'msg_limit' => 1, 'remove_read' => false));
    $form_data = !empty($form_data) ? current( $form_data ) : array();
    
    foreach($_POST as $key => $val){
        $form_data[$key] = htmlspecialchars(trim($val));
    }
    
    $modx->registry->form_to_session->subscribe( "/form_data/" );
    $modx->registry->form_to_session->send(
        "/form_data/",
        array( $session_id => $form_data ),
        array(
            //'kill' => true,
            'ttl' => 86400
            //'expires' => time() + 1440
        )
    );
    
    //Добавляем данные из другой формы (сохраненные в сессии) если нужно
    if( $prependFormData || $appendFormData ){
        
        
        if( empty( $form_data ) ){
            return true;
        }
        
        $prepend_html = '';
        $append_html = '';
        
        foreach( array( $prependFormData, $appendFormData ) as $key => $val ){
            
            if( !$val ) continue;
            
            $form_ids_arr = explode(',',$val);
            
            foreach( $form_ids_arr as $v ){
                
                $f_id = trim($v);
                if( is_numeric( $f_id ) ){
                    
                    $modx->addPackage('form_construct',$modx->getOption('core_path').'components/form_construct/model/');
                    
                    $form = $modx->getObject('formConstructItem',$f_id);
                    
                    if( $form ){
                        
                        $form_fields_data = $form->get('fields_data');
                        $form_fields_data = !empty($form_fields_data) ? json_decode($form_fields_data,true) : array();
                        $form_fields = array();
                        
                        foreach($form_fields_data as $k => $field){
                            
                            if($field['type'] == 'file') continue;
                            
                            $field_name = !empty($field['name']) ? $field['name'] : 'field' . $f_id . '_' . ($k+1);
                            
                            if( empty( $form_data[ $field_name ] ) ){
                                $form_data[ $field_name ] = '';
                            }
                            
                            $value = is_array($form_data[ $field_name ]) ? implode(', ',$form_data[ $field_name ]) : $form_data[ $field_name ];
                            $value = htmlspecialchars( $value );
                            $form_fields[ $field_name ] = $value;
                            
                            if( $field['type'] == 'email' ){
                                $hook->formit->config['fiarToField'] = 'email';
                                $hook->formit->config['emailReplyTo'] = $value;
                                $hook->setValue( 'email', $value );
                            }
                            
                            $chunkArr = array(
                                'caption' => $field['caption'],
                                'value' => $value
                            );
                            
                            if( $key > 0 ){
                                
                                $append_html .= $modx->parseChunk( $emailRowTpl, $chunkArr );
                                
                            }else{
                                
                                $prepend_html .= $modx->parseChunk( $emailRowTpl, $chunkArr );
                                
                            }
                            
                        }
                        
                        $hook->setValues( $form_fields );
                        
                    }
                    
                }
                
            }
            
        }
        
        $hook->setValues(
            array(
                'formit.rows_prepend' => $prepend_html,
                'formit.rows_append' => $append_html
            )
        );
        
    }
    
}else{
    
    $modx->registry->form_to_session->subscribe("/form_data/{$session_id}/");
    $form_data = $modx->registry->form_to_session->read(array('poll_limit' => 1, 'msg_limit' => 1, 'remove_read' => false));
    $form_data = !empty($form_data) ? current( $form_data ) : array();
    
    //вытаскиваем личную информацию пользователя
    if( $getUserData ){
        $userid = $modx->getLoginUserID();
        if($userid){
            $user = $modx->getUser('web');
            $profile = $user->getOne('Profile');
            foreach ($profile->toArray() as $key => $value) {
                if(empty($form_data[$key])) $form_data[$key] = $value;
            }
            unset($key,$val);
        }
    }
    
    if(!empty($form_data)) $hook->setValues( $form_data );
    
}

return $output;