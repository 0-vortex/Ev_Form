<?php

class Ev_Form
{

    public $error = array();

    private $checkboxRadio = array(
        'checkbox',
        'radio'
    );

    private static $regexp = array(
            'text' => array(
                    'rule' => '%^[\p{L}\p{M}\p{P}\s]+$%',
                    'message' => 'Campul poate contine numai litere si semne de punctuatie.'
            ),
            'name' => array(
                    'rule' => '%^[\p{L}\p{M}\p{P}\s]+$%',
                    'message' => 'Campul poate contine numai litere, spatii si semne de punctuatie.'
            ),
            'username' => array(
                    'rule' => '%^[\p{L}\p{N}]$%',
                    'message' => 'Campul poate contine numai litere si cifre.'
            ),
            'password' => array(
                    'rule' => '%^\A(?=\S*?[a-zA-Z])(?=\S*?[0-9])\S{8,}\z$%',
                    'message' => 'Campul trebuie sa fie de cel putin 8 litere, cu cel putin o litera si o cifra.'
            ),
            'phone' => array(
                    'rule' => '%\(?\b[0-9]{3,4}\)?[-\. ]?[0-9]{3}[-\. ]?[0-9]{3}\b%',
                    'message' => 'Campul poate contine numai cifre si semnele "()-. ".'
            ),
            'email' => array(
                    'rule' => '!^([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|\\x22([^\\x0d\\x22\\x5c\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x22)(\\x2e([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|\\x22([^\\x0d\\x22\\x5c\\x80-\\xff]|\\x5c\\x00-\\x7f)*\\x22))*\\x40([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|\\x5b([^\\x0d\\x5b-\\x5d\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x5d)(\\x2e([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|\\x5b([^\\x0d\\x5b-\\x5d\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x5d))*$!',
                    'message' => 'Emailul este invalid.'
            ),
            'textarea' => array(
                    'rule' => '%^[\p{L}\p{M}\p{N}\p{P}\p{S}\p{Z}\r\n\t]+$%ismu',
                    'message' => 'Campul nu poate contine litere invalide.'
            ),
            'zip' => array(
                    'rule' => '%[\p{N}]{6}%',
                    'message' => 'Codul postal este numeric si are o lungime de 6 cifre.'
            ),
            'single_checkbox' => array(
                    'rule' => '%^(1|on)$%',
                    'message' => 'Este necesar sa bifati aceasta casuta.'
            ),
            'numeric' => array(
                    'rule' => '%^[\p{N}]+$%',
                    'message' => 'Campul este numeric.'
            ),
            'url' => array(
                    'rule' => '%\b((?#protocol)https?|ftp)://((?#domain)[\-a-zA-Z0-9\.]+)((?#file)/[\-a-zA-Z0-9+&@#/\%=~_|!:,.;]*)?((?#parameters)\?[a-zA-Z0-9+&@#/\%=~_|!:,.;]*)?%',
                    'message' => 'Este necesar sa introduceti un url valid.'
            ),
            'radio' => array(
                    'rule' => '%^([a-zA-Z0-9]+)$%',
                    'message' => 'Este necesar sa bifati cel putin o casuta.'
            ),
            'date' => array(
                    'rule' => '%^([0-9]{2})[\/\.]([0-9]{2})[\/\.]([0-9]{4})$%',
                    'message' => 'Data nu este corecta.'
            ),
            'iban' => array(
                    'rule' => '%^[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}$%',
                    'message' => 'IBAN-ul nu este corect.'
            )
    );

    private static $Instance;

    public static function getInstance ()
    {
        if (! self::$Instance) {
            self::$Instance = new Ev_Form();
        }

        return self::$Instance;
    }

    public function get_error_message ($n)
    {
        if (isset($this->error[$n['id']]) && $this->error[$n['id']] != '')
        {
            return '<p class="error-message">' . $this->error[$n['id']] . '</p>';
        }

        return false;
    }

    public function get_title ($n)
    {
        $h = $f = '';

        if (((isset($n['label']) && $n['label'])
             || ! isset($n['label']))
            && isset($n['name']) && $n['name'])
        {
            $h = '<label for="' . $n['id'] . '">';
            $f = '</label>';

            return $h . (isset($n['lang']) && $n['lang'] ? tr($n['name']) : $n['name'])
                 . $f . ((isset($n['required']) && $n['required']) || ! isset($n['required']) ? ' <span>*</span>' : '');
        }

        return false;
    }

    public function get_value ($n, $key = 0, $array_values = null)
    {
        if (isset($n['type']) && $n['type'] == 'radio')
        {
            return 'value="' . $key . '"'
                 . (isset($array_values[$n['id']]) && $key == $array_values[$n['id']] ? ' checked="checked"' : '');
        }

        elseif (isset($this->error[$n['id']]) && $this->error[$n['id']] != ''
                && (! isset($n['type']) || $n['type'] != 'checkbox'))
        {
            return '';
        }

        else {
            if ($n['tag'] == 'input')
            {
                if (isset($n['type']) && in_array($n['type'], array('text', 'password', 'email')))
                {
                    return 'value="' . (isset($array_values[$n['id']]) ? $array_values[$n['id']] : '') . '"';
                }

                if (isset($n['type']) && $n['type'] == 'checkbox')
                {
                    if (! isset($n['multiple']) || ! $n['multiple'])
                    {
                        return 'value="1"' . (isset($array_values[$n['id']]) && $array_values[$n['id']] == 1 ? ' checked="checked"' : '');
                    }

                    else {
                        return 'value="' . $key . '"'
                             . (isset($array_values[$n['id']]) && in_array($key, $array_values[$n['id']])
                                ? ' checked="checked"'
                                : '');
                    }
                }
            }

            elseif (in_array($n['tag'], array('textarea', 'select')))
            {
                if (isset($array_values[$n['id']])) {
                    return $array_values[$n['id']];
                }

                return false;
            }

            return false;
        }
    }

    public function get_attributes ($n, $key = 0)
    {
        $return = array();

        if (! isset($n['id'])) {
            return false;
        }

        else {
            if (isset($n['type']) && in_array($n['type'], $this->checkboxRadio) && isset($n['multiple']) && $n['multiple'])
            {
                $return[] = 'name="' . $n['id'] . '[]"';
            }

            else {
                $return[] = 'name="' . $n['id'] . '"';
            }

            if (isset($n['title']) && $n['title'] != '')
            {
                $return[] = 'title="' . $n['title'] . '"';
            }

            else {
                $n['title'] = $n['name'];
            }

            if (isset($n['lang']) && $n['lang']) {
                $n['name'] = tr($n['name']);
                $n['title'] = tr($n['title']);

                if (isset($n['placeholder']) && $n['placeholder'] != '')
                {
                    $n['placeholder'] = tr($n['placeholder']);
                }
            }

            if (isset($n['autofocus']) && $n['autofocus'])
            {
                $return[] = 'autofocus';
            }

            if (in_array($n['tag'], array('select', 'textarea'))
                || ($n['tag'] == 'input' && isset($n['type']) && ! in_array($n['type'], $this->checkboxRadio)))
            {
                $return[] = 'id="' . $n['id'] . '"';
            }

            if (isset($n['type']) && in_array($n['type'], $this->checkboxRadio))
            {
                if (isset($n['multiple']))
                {
                    if (! $n['multiple']) {
                        $return[] = 'id="' . $n['id'] . '"';
                    }

                    else {
                        $return[] = 'id="' . $n['id'] . '-' . $key . '"';
                    }
                }

                else {
                    $return[] = 'id="' . $n['id'] . '-' . $key . '"';
                }
            }

            if (((isset($n['required']) && $n['required']) || ! isset($n['required']))
                 && ! (isset($n['type']) && in_array($n['type'], $this->checkboxRadio) && isset($n['multiple']) && $n['multiple']))
            {
                $return[] = 'required="required"';
            }

            if (isset($n['readonly']) && $n['readonly'])
            {
                $return[] = 'readonly="readonly"';
            }

            if (isset($n['disabled']) && $n['disabled'])
            {
                $return[] = 'disabled="true"';
            }

            if ((isset($n['autocomplete']) && ! $n['autocomplete']) || ! isset($n['autocomplete']))
            {
                $return[] = 'autocomplete="off"';
            }

            if (isset($n['placeholder']) && $n['placeholder'] != '')
            {
                $return[] = 'placeholder="' . $n['placeholder'] . '"';
            }

            if (isset($n['maxlength']) && is_numeric($n['maxlength']))
            {
                $return[] = 'maxlength="' . $n['maxlength'] . '"';
            }

            if (isset($this->error[$n['id']]) && $this->error[$n['id']] != '')
            {
                $return[] = 'class="error"';
            }

            else {
                $return[] = 'class="normal"';
            }

            return implode(' ', $return);
        }
    }

    public function get_click ($n, $key)
    {
        if ($n['tag'] == 'input' && isset($n['options']) && is_array($n['options']))
        {
            if (! isset($n['lang']) || (isset($n['lang']) && $n['lang']))
            {
                foreach ($n['options'] as &$e)
                {
                    $e = tr($e);
                }
            }

            $h = $f = '';

            if ((isset($n['label']) && $n['label']) || ! isset($n['label']))
            {
                $h = '<label for="' . $n['id'] . '-' . $key . '">';
                $f = '</label>' . (isset($n['separator']) ? $n['separator'] : '');
            }

            return $h . $n['options'][$key] . $f;
        }

        return false;
    }

    public function get_tag ($n, $key = 0, $array_values = null)
    {
        $tag = $n['tag'];

        switch ($tag) {
            case $array[] = "input":
                if ($tag != null)
                {
                    $array = array(
                        'h' => '<input type="' . $n['type'] . '" '
                             . self::get_attributes($n, $key) . ' '
                             . self::get_value($n, $key, $array_values),
                        'c' => '',
                        'f' => '/>' . self::get_click($n, $key)
                    );

                    break;
                }

            case $array[] = "textarea":
                if ($tag != null)
                {
                    $array = array(
                        'h' => '<textarea ' . self::get_attributes($n),
                        'c' => '>' . self::get_value($n, $key, $array_values),
                        'f' => '</textarea>'
                    );

                    break;
                }

            case $array[] = "select":
                if ($tag != null)
                {
                    $array = array(
                        'h' => '<select ' . self::get_attributes($n) . '>',
                        'c' => self::get_options($n, $array_values),
                        'f' => '</select>'
                    );

                    break;
                }
        }

        return $array;
    }

    public function get_options ($n, $array_values = null)
    {
        $return = '';

        $value = self::get_value($n, 0, $array_values);

        if (! $value && isset($n['default']))
        {
            $value = $n['default'];
        }

        if (isset($n['options']) && ! empty($n['options']))
        {
            foreach ($n['options'] as $o)
            {
                $return .= '<option value="' . $o['id'] . '"'
                         . (strval($o['id']) === strval($value) ? ' selected="selected"' : '')
                         . '">' . $o['name'] . '</option>';
            }
        }

        return $return;
    }

    public function get_container ($n)
    {
        $tag = (isset($n['wrapper']) ? $n['wrapper'] : 'definition list');

        switch ($tag) {
            case $array[] = 'div':
                if ($tag != null)
                {
                    $array = array(
                        'h' => '<div>' . (isset($n['class']) && ! empty($n['class']) ? 'class="' . $n['class'] . '"' : '') . '><dt>',
                        'c' => '</dt><dd>',
                        'f' => '</dd></div>'
                    );

                    break;
                }

            case $array[] = 'definition list':
                if ($tag != null)
                {
                    $array = array(
                            'h' => '<dt>',
                            'c' => '</dt><dd>',
                            'f' => '</dd>'
                    );

                    break;
                }

            case $array[] = 'paragraph':
                if ($tag != null)
                {
                    $array = array(
                            'h' => '<p>',
                            'c' => '',
                            'f' => '</p>'
                    );

                    break;
                }
        }

        return $array;
    }

    public function get_wrapper ($n)
    {
        $tag = (isset($n['wrapper']) ? $n['wrapper'] : 'definition list');
        $n['class'] = (isset($n['class']) ? $n['class'] : 'cf');
        $inside = $outside = '';

        if (isset($n['heading']) && ! empty($n['heading']))
        {
            $inside = $outside = '<' . $n['heading_tag'] . '>' . $n['heading'] . '</' . $n['heading_tag'] . '>';

            if (! isset($n['inside']) xor (isset($n['inside']) && ! $n['inside']))
            {
                $inside = '';
            }

            else {
                $outside = '';
            }
        }

        switch ($tag) {
            case $array[] = 'definition list':
                if ($tag != null) {
                    $array = array(
                        'h' => $outside . '<dl '
                             . (isset($n['class']) && ! empty($n['class']) ? ' class="' . $n['class'] . '"' : '')
                             . (isset($n['id']) && ! empty($n['id']) ? ' id="' . $n['id'] . '"' : '')
                             . '>' . $inside,
                        'f' => '</dl>'
                    );

                    break;
                }

            case $array[] = 'paragraph':
                if ($tag != null) {
                    $array = array(
                        'h' => $outside . '<div '
                             . (isset($n['class']) && ! empty($n['class']) ? ' class="' . $n['class'] . '"' : '')
                             . (isset($n['id']) && ! empty($n['id']) ? ' id="' . $n['id'] . '"' : '')
                             . '>' . $inside,
                        'f' => '</div>'
                    );

                    break;
                }
        }

        return $array;
    }

    public function factory ($master, $array_values = null)
    {
        if ($array_values === null)
        {
            $array_values = $_REQUEST;
        }

        if (empty($master))
        {
            return false;
        }

        else {
            $return = '';

            foreach ($master as $form)
            {
                $wrapper = self::get_wrapper($form);
                $container = self::get_container($form);

                $return_child = '';

                foreach ($form['elements'] as $n)
                {

                    if (! isset($n['id']) || ! isset($n['tag']) || ! isset($n['name']))
                    {
                        break;
                    }

                    else {
                        $title = self::get_title($n);

                        if ($n['tag'] == 'input' && in_array($n['type'], $this->checkboxRadio)
                            && isset($n['options']) && is_array($n['options']))
                        {
                            $tag = array();

                            foreach ($n['options'] as $key => $value)
                            {
                                $tag[$key] = self::get_tag($n, $key, $array_values);
                            }
                        }

                        else {
                            $tag = self::get_tag($n, 0, $array_values);
                        }

                        $error = ((isset($n['errors']) && $n['errors']) || ! isset($n['errors'])
                                  ? self::get_error_message($n)
                                  : '');

                        $return_child .= $container['h'] . $title . $container['c'];

                        if ($n['tag'] == 'input' && in_array($n['type'], $this->checkboxRadio) && isset($n['options'])
                            && is_array($n['options']))
                        {
                            foreach ($n['options'] as $key => $value)
                            {
                                $return_child .= $tag[$key]['h'] . $tag[$key]['c'] . $tag[$key]['f'];
                            }
                        }

                        else {
                            $return_child .= $tag['h'] . $tag['c'] . $tag['f'];
                        }

                        $return_child .= $error . $container['f'] . PHP_EOL;
                    }
                }

                $return .= $wrapper['h'] . $return_child . $wrapper['f'];
            }

            return $return;
        }
    }

    public function validate ($master, $array_values)
    {
        if (empty($master))
        {
            return false;
        }

        else {
            foreach ($master as $form)
            {
                foreach ($form['elements'] as $n)
                {
                    if (! isset($n['id']) || ! isset($n['tag']) || ! isset($n['name']))
                    {
                        break;
                    }

                    else {
                        if ((isset($n['required']) && $n['required'])
                            || (isset($array_values[$n['id']]) && $array_values[$n['id']] != ''))
                        {
                            if (isset($n['type']) && in_array($n['type'], array('checkbox','radio'))
                                && isset($n['multiple']) && $n['multiple'])
                            {
                                if (! isset($array_values[$n['id']])
                                    || (isset($array_values[$n['id']]) && empty($array_values[$n['id']])))
                                {
                                    $message = 'Este necesar sa bifati cel putin o casuta!';

                                    if (isset($n['lang']) && $n['lang'])
                                    {
                                        $message = tr($message);
                                    }

                                    $this->error[$n['id']] = $message;
                                }
                            }

                            elseif (self::validate_regexp($n,$array_values)
                                    || (isset($n['link']) && $array_values[$n['id']] != $array_values[$n['link']]))
                            {
                                $message = self::validate_message($n, $form);

                                if ((isset($n['required']) && $n['required'])
                                    || (isset($n['regexp']) && self::get_value($n) != ''))
                                {
                                    $this->error[$n['id']]=$message;
                                }
                            }
                        }
                    }
                }
            }

            return $this->error;
        }
    }

    public function validate_options ($n, $array_values)
    {
        $accepted_values = array();

        if (isset($n['options']) && is_array($n['options']))
        {
            foreach ($n['options'] as $option) {
            }
        }
    }

    public function validate_regexp ($n, $array_values)
    {
        if (isset($n['regexp']) && array_key_exists($n['regexp'], self::$regexp))
        {
            if (! isset($array_values[$n['id']])
                xor (isset($array_values[$n['id']])
                     && ! preg_match(self::$regexp[$n['regexp']]['rule'], $array_values[$n['id']])))
            {
                return true;
            }
        }

        return false;
    }

    public function validate_message ($n, $form)
    {
        if (empty($form)) {
            return false;
        }

        else {
            if (isset($n['link']))
            {
                $message = 'Urmatoarele campuri nu coincid: ';
                $errors[] = '{' . $n['name'] . '}';

                foreach ($form['elements'] as $nn)
                {
                    if ($nn['id'] == $n['link'])
                    {
                        $errors[] = '{' . $nn['name'] . '}';
                    }
                }

                if (isset($n['lang']) && $n['lang'])
                {
                    $message = tr($message);

                    foreach ($errors as &$e)
                    {
                        $e = tr($e);
                    }
                }

                $message .= implode(', ', $errors);
            }

            elseif (isset($n['error_message']))
            {
                $message = (isset($n['lang']) && $n['lang'] ? tr($n['error_message']) : $n['error_message']);
            }

            else {
                $message = self::$regexp[$n['regexp']]['message'];
                $message = (isset($n['lang']) && $n['lang']
                            ? '{' . tr($n['title']) . '} - ' . tr($message)
                            : '{' . ( isset($n['title']) ? $n['title'] : $n['name'] ) . '} - ' . $message);
            }

            return $message;
        }
    }
}
