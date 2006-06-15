<?php
class highlighter
{
    // Class taken from http://www.aesthetic-theory.com/learn.php?highlight

    var $code;
    var $adjust;
    var $line_numbers;
    var $highlight_method;

    function highlighter($method = 'highlight_string')
    {
        $this->highlight_method = $method;
        // defaults
        $this->adjust = true;
        $this->line_numbers = true;
    }

    function set_config($property, $value)
    {
        if (isset($this->$property))
        {
            $this->$property = $value;
            return true;
        }
        else
        {
            return false;
        }
    }

    function set_code($code)
    {
        $this->code = $code;
    }

    function erase()
    {
        $this->code = '';
    }

    function _make_css($code)
    {
        $code=preg_replace(
            '{([\w_]+)(\s*</font>)'.
            '(\s*<font\s+color="'.ini_get('highlight.keyword').'">\s*\()}m',
            '<a class="php_code_link" title="View manual page for $1" href="http://www.php.net/manual-lookup.php?lang=en&amp;pattern=$1">$1</a>$2$3',
            $code);

        $colors[ini_get('highlight.bg')] = 'php_background';
        $colors[ini_get('highlight.comment')] = 'php_comment';
        $colors[ini_get('highlight.default')] = 'php_default';
        $colors[ini_get('highlight.html')] = 'php_html';
        $colors[ini_get('highlight.keyword')] = 'php_keyword';
        $colors[ini_get('highlight.string')] = 'php_string';

        foreach($colors as $color=>$class)
        {
            $code = str_replace('<font color="' . $color . '">', '<span class="' . $class . '">', $code);
        }

        return str_replace('</font>', '</span>', $code);
    }

    function process()
    {
        $code = ($this->adjust && !strstr($this->code, '<?php')) ? '<?php' . "\n" . $this->code . "\n" . '?>' : $this->code;
        $hlfunc = $this->highlight_method;
        $code = $hlfunc($code, true);
        $code = $this->_make_css($code);
        $lines = count(explode('<br />',$code));
        $output = '<table id="showsource">' . "\n" . '<tr><td valign="top" class="num"><code>';
        for ($i = 1; $i < ($lines); $i++)
        {
            if ($this->line_numbers)
            {
                $output .= $i . '<br />';
            }
        }

        $output .= '</code></td><td valign="top" class="phpline">' . $code . '</td></tr></table>';

        return $output;
    }
}
?>
