<?php

/*

<select class="col-sm-8 form-control" name="field1" id="field1">
    <option value=""></option>
    <option value="value1" [[!+fi.field1:isSelected=`value1`]]>value1</option>
    <option value="value2" [[!+fi.field1:isSelected=`value2`]]>value2</option>
</select>

*/

$output = ' ';
if ($input == $options) {
    $output = ' selected="selected"';
}
return $output;