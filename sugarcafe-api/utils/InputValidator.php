<?php
class InputValidator {
    public static function validate($data, $rules) {
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && (!isset($data->$field) || empty($data->$field))) {
                return false;
            }
        }
        return true;
    }
}
?>

