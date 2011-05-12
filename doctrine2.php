<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
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
        if (strpos($this->type, "varchar") === 0) return "string";
        if (strpos($this->type, "smallint") === 0) return "smallint";
        if (strpos($this->type, "bigint") === 0) return "bigint";
        if (strpos($this->type, "int") === 0) return "integer";
        if (strpos($this->type, "boolean") === 0) return "boolean";
        if (strpos($this->type, "decimal") === 0) return "decimal";
        if (strpos($this->type, "datetime") === 0) return "datetime";
        if (strpos($this->type, "timestamp") === 0) return "datetime";
        if (strpos($this->type, "clob") === 0) return "text";

        if (strpos($this->type, "char") === 0) return "text";
        if (strpos($this->type, "text") === 0) return "text";
        if (strpos($this->type, "longtext") === 0) return "text";

		return "Unknown";
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
		$text=str_replace("#doctrinePrimitiveType#", $this->getDoctrineObjectType(), $text);
        $text=str_replace("#phpObjectType#", $this->getPHPObjectType(), $text);
		$text=str_replace("#indexName#", $this->getIndexName(), $text);

        $text=str_replace("#key#", "AUTO", $text);
        $text=str_replace("#ext#", "AUTO", $text);
        $text=str_replace("#nullable#", $this->nullable == "NO" ? "false" : "true", $text);
		return $text;
	}


}

    // inserts Doctrine metadata for field
    function insertFieldMetadata($tablePropertie)
    {
        $deliminator = '';
        $count = 0;
        $str  =  "\t/**\n";

        if($tablePropertie->key)
        {
            if($tablePropertie->isPK())
              $str .= "\t * @Id";
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
                else if($key == 'nullable')
                {
                    $str .=  $deliminator.$key.'="'.$tablePropertie->format('#nullable#').'"';
                }
                else if($key == 'type')
                {
                    $str .=  $deliminator.$key.'="'.$tablePropertie->format('#doctrinePrimitiveType#').'"';
                }
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
            $str .= "\n\t * @GeneratedValue(strategy='".$tablePropertie->format("#ext#")."')";

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
           // $lines[] = print_r($tableProperties);
            $lines[] = "<?php\n";
			$lines[] = "namespace models;";
            $lines[] = "\n/**\n * @Entity\n * @Table(name='".$table."')\n */\n";

			$lines[] = "class ".ucfirst($table).' {';

			foreach ($tableProperties as $tablePropertie)
            {
                $lines[] =  insertFieldMetadata($tablePropertie);
                $lines[] = $tablePropertie->format("	private $#name#;\n");

            }

			$lines[] = "\n";
			foreach ($tableProperties as $tablePropertie)
            {
                $lines[] = $tablePropertie->format("	public function get#name#() \n\t{\n\t\treturn \$this->#name#;\n\t} \n");
                $lines[] = $tablePropertie->format("	public function set#name#($#name#) \n\t{\n\t\t\$this->#name# = \$#name#;\n\t} \n");
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
