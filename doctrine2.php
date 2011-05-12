<?php

/**
 * Set of functions used to build Doctrine 2 dumps of tables
 *
 * @package phpMyAdmin-Export-Doctrine2 PHP
 *
 * @author jerome Erasmus 2011 www.developermill.com
 */

if (! defined('PHPMYADMIN')) {
    exit;
}

/**
 * This gets executed twice so avoid a notice
 */
if (! defined('D2_FORMAT_PHP')) {
    define("D2_FORMAT_PHP", "Doctrine 2 PHP");
    define("D2_FORMAT_XML", "Doctrine 2 XML");

    define("D2_HANDLER_PHP_BODY", "handleDoctrine2PHPBody");
    define("D2_HANDLER_XML_BODY", "handleDoctrine2XMLBody");
}

$CG_FORMATS = array(D2_FORMAT_PHP, D2_FORMAT_XML);
$CG_HANDLERS = array(D2_HANDLER_PHP_BODY, D2_HANDLER_XML_BODY);

/**
 * Export Options
 */
if (isset($plugin_list)) {
    $plugin_list['doctrine2'] = array(
        'text' => 'Doctrine 2',
        'extension' => 'cs',
        'mime_type' => 'text/cs',
          'options' => array(
          	array('type' => 'hidden', 'name' => 'data'),
            array('type' => 'select', 'name' => 'format', 'text' => 'strFormat', 'values' => $CG_FORMATS),
            ),
        'options_text' => 'strOptions',
        );
} else {

/**
 * Set of functions used to build exports of tables
 */

/**
 * Outputs comment
 *
 * @param   string      Text of comment
 *
 * @return  bool        Whether it suceeded
 */
function PMA_exportComment($text)
{
    return TRUE;
}

/**
 * Outputs export footer
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
function PMA_exportFooter()
{
    return TRUE;
}

/**
 * Outputs export header
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
function PMA_exportHeader()
{
    return TRUE;
}

/**
 * Outputs database header
 *
 * @param   string      Database name
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
function PMA_exportDBHeader($db)
{
    return TRUE;
}

/**
 * Outputs database footer
 *
 * @param   string      Database name
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
function PMA_exportDBFooter($db)
{
    return TRUE;
}

/**
 * Outputs create database database
 *
 * @param   string      Database name
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
function PMA_exportDBCreate($db)
{
    return TRUE;
}

/**
 * Outputs the content of a table in Doctrine 2 format
 *
 * @param   string      the database name
 * @param   string      the table name
 * @param   string      the end of line sequence
 * @param   string      the url to go back in case of error
 * @param   string      SQL query for obtaining data
 *
 * @return  bool        Whether it suceeded
 *
 * @access  public
 */
function PMA_exportData($db, $table, $crlf, $error_url, $sql_query)
{
	global $CG_FORMATS, $CG_HANDLERS;
	$format = cgGetOption("format");
	$index = array_search($format, $CG_FORMATS);
	if ($index >= 0)
		return PMA_exportOutputHandler($CG_HANDLERS[$index]($db, $table, $crlf));
	return PMA_exportOutputHandler(sprintf("%s is not supported.", $format));
}

/**
 *
 * @package phpMyAdmin-Export-Codegen
 */
class TableProperty
{
	public $name;
	public $type;
	public $nullable;
	public $key;
	public $defaultValue;
	public $ext;
	function __construct($row)
	{
		$this->name = trim($row[0]);
		$this->type = trim($row[1]);
		$this->nullable = trim($row[2]);
		$this->key = trim($row[3]);
		$this->defaultValue = trim($row[4]);
		$this->ext = trim($row[5]);
	}
	function getPureType()
	{
		$pos=strpos($this->type, "(");
		if ($pos > 0)
			return substr($this->type, 0, $pos);
		return $this->type;
	}
	function isNotNull()
	{
		return $this->nullable == "NO" ? "true" : "false";
	}
	function isUnique()
	{
		return $this->key == "PRI" || $this->key == "UNI" ? "true" : "false";
	}
	function getPHPPrimitiveType()
	{
		if (strpos($this->type, "int") === 0) return "int";
		if (strpos($this->type, "long") === 0) return "long";
		if (strpos($this->type, "char") === 0) return "string";
		if (strpos($this->type, "varchar") === 0) return "string";
		if (strpos($this->type, "text") === 0) return "string";
		if (strpos($this->type, "longtext") === 0) return "string";
		if (strpos($this->type, "tinyint") === 0) return "bool";
		if (strpos($this->type, "datetime") === 0) return "DateTime";
		return "unknown";
	}
	function getPHPObjectType()
	{
		if (strpos($this->type, "int") === 0) return "Int32";
		if (strpos($this->type, "long") === 0) return "Long";
		if (strpos($this->type, "char") === 0) return "String";
		if (strpos($this->type, "varchar") === 0) return "String";
		if (strpos($this->type, "text") === 0) return "String";
		if (strpos($this->type, "longtext") === 0) return "String";
		if (strpos($this->type, "tinyint") === 0) return "Boolean";
		if (strpos($this->type, "datetime") === 0) return "DateTime";
		return "Unknown";
	}

    /*
     *
     * Doctrine Object Type Conversion
     */
    function getDoctrineObjectType()
	{
         $obj = new stdClass;

        // extract type
       if (strpos($this->type, "varchar") === 0) $obj->type = "string";
        if (strpos($this->type, "smallint") === 0) $obj->type = "smallint";
        if (strpos($this->type, "bigint") === 0) $obj->type = "bigint";
        if (strpos($this->type, "int") === 0) $obj->type = "integer";
        if (strpos($this->type, "boolean") === 0) $obj->type = "boolean";
        if (strpos($this->type, "decimal") === 0) $obj->type = "decimal";
        if (strpos($this->type, "datetime") === 0) $obj->type = "datetime";
        if (strpos($this->type, "timestamp") === 0) $obj->type = "datetime";
        if (strpos($this->type, "clob") === 0) $obj->type = "text";

        if (strpos($this->type, "char") === 0) $obj->type = "text";
        if (strpos($this->type, "text") === 0) $obj->type = "text";
        if (strpos($this->type, "longtext") === 0) $obj->type = "text";

        //extract value if any
        $sS = strrpos($this->type, '(');
        $sE = strrpos($this->type, ')');

        if($sS && $sE)
            $obj->val = substr($this->type, $sS+1, ($sE-$sS)-1);

		return $obj;
	}



	function getIndexName()
	{
		if (strlen($this->key)>0)
			return "index=\"" . $this->name . "\"";
		return "";
	}
	function isPK()
	{
		return $this->key=="PRI";
	}
	function format($pattern)
	{
		$text=$pattern;
		$text=str_replace("#name#", $this->name, $text);
		$text=str_replace("#type#", $this->getPureType(), $text);
		$text=str_replace("#notNull#", $this->isNotNull(), $text);
		$text=str_replace("#unique#", $this->isUnique(), $text);
		$text=str_replace("#ucfirstName#", ucfirst($this->name), $text);
		$text=str_replace("#phpPrimitiveType#", $this->getPHPPrimitiveType(), $text);
        $text=str_replace("#phpObjectType#", $this->getPHPObjectType(), $text);
		$text=str_replace("#indexName#", $this->getIndexName(), $text);

        $text=str_replace("#key#", "AUTO", $text);
        $text=str_replace("#ext#", "AUTO", $text);
        $text=str_replace("#nullable#", $this->nullable == "NO" ? "false" : "true", $text);
		return $text;
	}


}


     /**
     * Translates a camel case string into a string with underscores
     */
     function fromCamelCase($str)
     {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
     }

    /**
     * Translates a string with underscores into camel case
     */
    function toCamelCase($str, $capFirstChar = false)
    {
        $str = strtolower($str);
        if($capFirstChar) {
          $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    // inserts Doctrine metadata for field
    function insertFieldMetadata($tablePropertie)
    {
        $deliminator = '';
        $count = 0;
        $str  =  "\t/**";

        if($tablePropertie->key)
        {
            if($tablePropertie->isPK())
              $str .= "\n\t * @Id";
        }

        $str .=  "\n\t * @Column(";
        foreach ($tablePropertie as $key => $val)
        {
            if(!empty($val))
            {
               if($count > 0 )
                    $deliminator = ', ';

                // Do Property mapping to doctrine type properties
                if($key == 'name')
                {
                   $str .=  $deliminator.$key.'="'.$tablePropertie->format('#name#').'"';
                }
                else if($key == 'type')
                {
                    $typeObj = $tablePropertie->getDoctrineObjectType();

                    // display type
                    $str .=  $deliminator.$key.'="'.$typeObj->type.'"';

                    // display type value
                    if($typeObj->val)
                        $str .=  $deliminator.'length='.$typeObj->val;
                }
                else if($key == 'nullable')
                {
                    $str .=  $deliminator.$key.'='.$tablePropertie->format('#nullable#');
                }
              /*  else if($key == 'unique')
                {
                    $str .=  $deliminator.$key.'='.$tablePropertie->format('#unique#');
                }  */
                else if($key == 'ext')
                {
                    // inline ignore
                }
                else if($key == 'key')
                {
                    // inline ignore
                }
                else
                {
                    $str .=  $deliminator.$key.'="'.$val.'"';
                }

            }

            $count++;
        }

        $str .=  ")";

        if($tablePropertie->ext)
            $str .= "\n\t * @GeneratedValue(strategy=\"".$tablePropertie->format("#ext#")."\")";

        $str .=  "\n\t */";

        return $str;
    }



	function handleDoctrine2PHPBody($db, $table, $crlf)
	{
        $lines=array();
		$result=PMA_DBI_query(sprintf("DESC %s.%s", PMA_backquote($db), PMA_backquote($table)));

       //  $sql = sprintf("DESC %s.%s", PMA_backquote($db), PMA_backquote($table);

        if ($result)
		{
			$tableProperties=array();
			while ($row = PMA_DBI_fetch_row($result))
            {
               $tableProperties[] = new TableProperty($row);
            }

            $lines[] = "<?php\n";
			$lines[] = "namespace models;";
            $lines[] = "\n/**\n * @Entity\n * @Table(name=\"".$table."\")\n */\n";

			$lines[] = "class ".toCamelCase( $table, true ).' {';

			foreach ($tableProperties as $tablePropertie)
            {
                $lines[] =  insertFieldMetadata($tablePropertie);

                $functname = toCamelCase( $tablePropertie->format('#name#') );
                $lines[] = "	private $".$functname.";\n";
            }

			$lines[] = "\n";
			foreach ($tableProperties as $tablePropertie)
            {
                $varname   = toCamelCase( $tablePropertie->format('#name#') );
                $functname = toCamelCase( $tablePropertie->format('#name#'), true );

                $lines[] = $tablePropertie->format("	public function get".$functname."() \n\t{\n\t\treturn \$this->".$varname.";\n\t} \n");
                $lines[] = $tablePropertie->format("	public function set".$functname."($".$varname.") \n\t{\n\t\t\$this->".$varname." = \$".$varname.";\n\t} \n");
            }

			$lines[] = "}\n?>\n\n";


			PMA_DBI_free_result($result);
		}
		return implode("\n", $lines);

	}

	function handleDoctrine2XMLBody($db, $table, $crlf)
	{

	}

	function cgGetOption($optionName)
	{
		global $what;
		return $GLOBALS[$what . "_" . $optionName];
	}
}
?>
