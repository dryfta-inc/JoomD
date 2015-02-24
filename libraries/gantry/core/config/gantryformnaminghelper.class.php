<?php
/**
 * @version   3.2.20 June 19, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class GantryFormNamingHelper
{
    static $instance;

    public static function getInstance()
    {

        if (empty(self::$instance))
        {
            self::$instance = new GantryFormNamingHelper();
        }
        return self::$instance;
    }

    function get_field_id($fieldId, $group = null)
    {
        global $gantry;


        // Initialize variables.
        $id = '';

        // If there is a form control set for the attached form add it first.
        //		if ($this->formControl) {
        //			$id .= $this->formControl;
        //		}

        // If the field is in a group add the group control to the field id.
        if ($group)
        {
            // If we already have an id segment add the group control as another level.
            if ($id)
            {
                $id .= '_' . str_replace('.', '_', $group);
            }
            else
            {
                $id .= str_replace('.', '_', $group);
            }
        }

        // If we already have an id segment add the field id/name as another level.
        if ($id)
        {
            $id .= '_' . $fieldId;
        }
        else
        {
            $id .= $fieldId;
        }

        // Clean up any invalid characters.
        $id = preg_replace('#\W#', '_', $id);

        return 'jform_params_' . $id;
    }

    function get_field_name($fieldName, $group = null)
    {
        global $gantry;

        $name = 'jform[params]';

        // If there is a form control set for the attached form add it first.
        //		if ($this->formControl) {
        //			$name .= $this->formControl;
        //		}

        // If the field is in a group add the group control to the field name.
        if ($group)
        {
            // If we already have a name segment add the group control as another level.
            $groups = explode('.', $group);
            if ($name)
            {
                foreach ($groups as $group)
                {
                    $name .= '[' . $group . ']';
                }
            }
            else
            {
                $name .= array_shift($groups);
                foreach ($groups as $group)
                {
                    $name .= '[' . $group . ']';
                }
            }
        }

        // If we already have a name segment add the field name as another level.
        if ($name)
        {
            $name .= '[' . $fieldName . ']';
        }
        else
        {
            $name .= $fieldName;
        }

        // If the field should support multiple values add the final array segment.
        //		if ($this->multiple) {
        //			$name .= '[]';
        //		}

        return $name;
    }
}
