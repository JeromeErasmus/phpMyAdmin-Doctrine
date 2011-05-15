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
if (! defined('D1_FORMAT_PHP')) {
    define("D1_FORMAT_PHP", "Doctrine 1.2 YAML");

    define("D1_HANDLER_PHP_BODY", "handleDoctrineBody");
}

$PARSE_FORMATS = array(D1_FORMAT_PHP);
$PARSE_HANDLERS = array(D1_HANDLER_PHP_BODY);

$YAML_dataTypes;

/**
 * Export Options
 */
if (isset($plugin_list)) {
    $plugin_list['doctrine1'] = array(
        'text' => 'Doctrine 1.2',
        'extension' => '.php.txt',
        'mime_type' => 'text/php',
          'options' => array(
          	array('type' => 'hidden', 'name' => 'data'),
            array('type' => 'select', 'name' => 'format', 'text' => 'strFormat', 'values' => $PARSE_FORMATS),
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
 *
    abstract 	Whether or not to make the generated class abstract. Defaults to false. When a class is abstract it is not exported to the database.
    className 	Name of the class to generate
    tableName 	Name of the table in your DBMS to use.
    connection 	Name of the Doctrine_Connection instance to bind the model to.
    columns 	Column definitions.
    relations 	Relationship definitions.
    indexes 	Index definitions.
    attributes 	Attribute definitions.
    actAs 	ActAs definitions.
    options 	Option definitions.
    inheritance 	Array for inheritance definition
    listeners 	Array defining listeners to attach
    checks 	Checks to run at application level as well as exporting to your DBMS
 */
function PMA_exportHeader()
{
    $str = "options:\n  type: INNODB\n\n";
    return PMA_exportOutputHandler($str);
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
	global $PARSE_FORMATS, $PARSE_HANDLERS;
	$format = cgGetOption("format");
	$index = array_search($format, $PARSE_FORMATS);
	if ($index >= 0)
    {
        $str = '';
        $str .= PMA_exportOutputHandler($PARSE_HANDLERS[$index]($db, $table, $crlf));
    }
		return $str;
	return PMA_exportOutputHandler(sprintf("%s is not supported.", $format));
}





/*
 *  ---- YAML COLUMN SCHEMA ----
    name 	Name of the column.
    fixed 	Whether or not the column is fixed.
    primary 	Whether or not the column is a part of the primary key.
    autoincrement 	Whether or not the column is an autoincrement column.
    type 	Doctrine data type of the column
    length 	Length of the column
    default 	Default value of the column
    scale 	Scale of the column. Used for the decimal type.
    values 	List of values for the enum type.
    comment 	Comment for the column.
    sequence 	Sequence definition for column.
    zerofill 	Whether or not to make the column fill empty characters with zeros
    extra 	Array of extra information to store with the column definition
    unsigned 	Unsigned modifiers for some field definitions, although not all DBMS's support this modifier for integer field types.
 */


/*
 *  ---- YAML DataType SCHEMA ----
 *  Below creates the available column types that can is used for the YAML data Type conversion
 *  as well as the type it is translated to when using the MySQL
 *
 */

function createYAML_dataTypeSchema()
{
     $type = array();
     $type['integer'] = 'integer';
     $type['tinyint'] = 'integer';
     $type['smallint'] = 'integer';
     $type['mediumint'] = 'integer';
     $type['int'] = 'integer';
     $type['bigint'] = 'integer';
     $type['double'] = 'float';  // ? correct schema?
//   $type['double'] = 'double'; // ? correct schema?
     $type['decimal'] = 'decimal';
     $type['char'] = 'char';
//   $type['varchar'] = 'varchar'; // ? correct schema?
     $type['varchar'] = 'string';  // ? correct schema?
     $type['text'] = 'array';    // ? correct schema?
//   $type['text'] = 'object';   // ? correct schema?
     $type['longblob'] = 'blob';
     $type['tinyblob'] = 'blob';
     $type['blob'] = 'blob';
     $type['mediuumblob'] = 'blob';
     $type['longtext'] = 'clob';
     $type['tinytext'] = 'clob';
     $type['text'] = 'clob';
     $type['mediumtext'] = 'clob';
     $type['datetime'] = 'timestamp';
     $type['time'] = 'time';
     $type['date'] = 'date';
//   $type['text'] = 'gzip';  // ? correct schema?
//   $type['tinyint(1)'] = 'boolean';  // ? correct schema?
     $type['bit'] = 'bit';
     //varbit ?
     //inet ?
     //enum ?

    return $type;
}

/**
 *
 * @package phpMyAdmin-Export-Doctrine
 */
class TableProperty
{
    public $fields;

	function __construct($rowObj)
	{

        $this->fields = new stdClass();

        $this->fields->name = $this->mapEntity('COLUMN_NAME', 'name', $rowObj, false);
        $this->fields->fixed = $this->mapEntity('', 'fixed', $rowObj, false);
        $this->fields->primary = $this->mapEntity('COLUMN_KEY', 'primary', $rowObj);
        $this->fields->autoincrement = $this->mapEntity('EXTRA', 'autoincrement', $rowObj);
        $this->fields->type = $this->mapEntity('DATA_TYPE', 'type', $rowObj);
        $this->fields->length = $this->mapEntity('CHARACTER_MAXIMUM_LENGTH', 'length', $rowObj, false);
        $this->fields->default = $this->mapEntity('COLUMN_DEFAULT', 'default', $rowObj);
        $this->fields->scale = $this->mapEntity('NUMERIC_SCALE', 'scale', $rowObj);
        $this->fields->values =  $this->mapEntity('', 'enum', $rowObj, false);     // ??? needs identification
        $this->fields->comment = $this->mapEntity('COLUMN_COMMENT', 'comment', $rowObj);
        $this->fields->sequence = $this->mapEntity('', 'sequence', $rowObj, false);  // ??? needs identification
        $this->fields->zerofill = $this->mapEntity('', 'zerofill', $rowObj, false);  // ??? needs identification
        $this->fields->extra = $this->mapEntity('EXTRA', 'extra', $rowObj);
        $this->fields->unsigned = $this->mapEntity('', 'unsigned', $rowObj, false); // ??? needs identification
	}

    function mapEntity($columnName, $columnSchemaName, $entityObj, $include=true)
    {
        global $YAML_dataTypes;

        $prop = new stdClass();

        $prop->schemaName = $columnSchemaName;
        $prop->include = $include;

        switch($columnSchemaName)
        {
             case "type":
                $prop->schemaVal = $YAML_dataTypes[$entityObj[$columnName]];
                break;

             case "autoincrement":
                $prop->schemaVal =  $entityObj[$columnName] == "auto_increment" ? "true" : "";
                break;

             case "primary":
                $prop->schemaVal =  $entityObj[$columnName] == "PRI" ? "true" : "";
                break;

             case "extra":
                if($entityObj[$columnName] == "auto_increment")
                    $prop->include = false;
                break;

             default:
                $prop->schemaVal = $entityObj[$columnName];
                break;
        }


        return $prop;
    }



    function getPureLength()
	{
		$sS = strrpos($this->fields->type->schemaName , '(');
        $sE = strrpos($this->fields->type->schemaName , ')');

        if($sS && $sE)
            return substr($this->fields->type->schemaName , $sS+1, ($sE-$sS)-1);

		return '';
	}


    /*
     * INSERT COLUMN
     *
     * @param   boolean      use verbose syntax
     */
    function insertColumn($useVerboseSyntax=true)
    {
        $lines = array();
        $lines[] = $this->fields->name->schemaVal.":";

        foreach($this->fields as $field => $val)
        {
            if(!empty($val->schemaVal) && $val->include == true)
            {
                $str = "      ".$val->schemaName.": ".$val->schemaVal;

                // append verbose length to type field e.g. varchar(255)
                if($useVerboseSyntax && $val->schemaName == 'type')
                {
                   $len = $this->getPureLength();

                   if(!empty($len))
                       $str .=  '('.$len.')';
                }

                $lines[] = $str;
            }
        }
        return implode("\n", $lines);
    }

}

    /*
     * ============================================================================
     */
	function handleDoctrineBody($db, $table, $crlf)
	{
        global $YAML_dataTypes;

        $lines=array();

        /*
         * Doctrine offers the ability to specify schema in an abbreviated syntax.
         *
         * If verbose is set to false, a lot of the schema parameters have values they default to,
         * this allows us to abbreviate the syntax and let Doctrine just use its defaults.
         *
         * If verbose is set to true ALL schema parameters will be included. This is recomended!
         */
        $useVerboseSyntax = true;

        // create schema
        $YAML_dataTypes = createYAML_dataTypeSchema();

        // build header
        if(!$useVerboseSyntax)
        {
            $lines[] = "detect_relations: true\n";
        }

        // build body

        $sqlQuery = "SELECT * FROM information_schema.columns WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table'";
		$result=PMA_DBI_query($sqlQuery);


        if ($result)
		{
			$tableProperties=array();
			while ($rowObj = PMA_DBI_fetch_assoc($result))
            {
               $tableProperties[] = new TableProperty($rowObj);
            }

            // insert table Class name
            $tableClass = toCamelCase($table, true);
            $lines[] = $tableClass.":";

            // insert table name
            $lines[] = "  tableName: ".$table;

            // insert columns
            $lines[] = "  columns:";
            foreach ($tableProperties as $tablePropertie)
            {
               // $lines[] = print_r($tablePropertie);
                $lines[] = "    ".$tablePropertie->insertColumn($useVerboseSyntax);
            }
            $lines[] = "\n";

            if($useVerboseSyntax)
            {
                // DO relations here
            }
			PMA_DBI_free_result($result);
		}
		return implode("\n", $lines);

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




	function cgGetOption($optionName)
	{
		global $what;
		return $GLOBALS[$what . "_" . $optionName];
	}
}
?>
