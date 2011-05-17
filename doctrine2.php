<?php

/**
 * Set of functions used to build Doctrine 1.2 YAML dumps of tables
 *
 * @package phpMyAdmin-Export-Doctrine1 PHP
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
    define("D2_FORMAT_PHP", "Doctrine 2.0 PHP");
    define("D2_FORMAT_XML", "Doctrine 2.0 XML");

    define("D2_HANDLER_PHP_BODY", "handleDoctrine2PHPBody");
    define("D2_HANDLER_XML_BODY", "handleDoctrine2XMLBody");
}

$PARSE_FORMATS = array(D2_FORMAT_PHP, D2_FORMAT_XML);
$PARSE_HANDLERS = array(D2_HANDLER_PHP_BODY, D2_HANDLER_XML_BODY);

$YAML_dataTypes;

/**
 * Export Options
 */
if (isset($plugin_list)) {
    $plugin_list['doctrine2'] = array(
        'text' => 'Doctrine 2.0',
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
    $str = "";
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
    columnKey 	Whether or not the column is a part of the primary / Unique etc..key.
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
    nullable
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
     $type['mediumblob'] = 'blob';
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
        $this->fields->autoincrement = $this->mapEntity('EXTRA', 'autoincrement', $rowObj);
        $this->fields->type = $this->mapEntity('DATA_TYPE', 'type', $rowObj);
        $this->fields->length = $this->mapEntity('COLUMN_TYPE', 'length', $rowObj);
        $this->fields->default = $this->mapEntity('COLUMN_DEFAULT', 'default', $rowObj);
        $this->fields->scale = $this->mapEntity('NUMERIC_SCALE', 'scale', $rowObj, false);
        $this->fields->precision = $this->mapEntity('NUMERIC_PRECISION', 'precision', $rowObj, false);
        $this->fields->columnKey = $this->mapEntity('COLUMN_KEY', 'columnKey', $rowObj);
        $this->fields->values =  $this->mapEntity('', 'enum', $rowObj, false);     // ??? needs identification
        $this->fields->comment = $this->mapEntity('COLUMN_COMMENT', 'comment', $rowObj);
        $this->fields->sequence = $this->mapEntity('', 'sequence', $rowObj, false);  // ??? needs identification
        $this->fields->zerofill = $this->mapEntity('', 'zerofill', $rowObj, false);  // ??? needs identification
        $this->fields->extra = $this->mapEntity('EXTRA', 'extra', $rowObj);
        $this->fields->unsigned = $this->mapEntity('', 'unsigned', $rowObj, false); // ??? needs identification
        $this->fields->nullable = $this->mapEntity('IS_NULLABLE', 'nullable', $rowObj);


        if($this->fields->type->schemaVal == "decimal")
        {
             $this->fields->scale->include = true;
             $this->fields->precision->include = true;
             $this->fields->length->include = false;
        }
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

             case "length":
                $prop->schemaVal = $this->getPureLength($entityObj[$columnName]);
                break;

             case "autoincrement":
                $prop->schemaVal =  $entityObj[$columnName] == "auto_increment" ? "true" : "";
                $prop->include = false;
                break;

             case "columnKey":
                if($entityObj[$columnName] == "PRI")
                {
                    $prop->schemaName = 'primary';
                    $prop->schemaVal =  "true";
                    $prop->include = false;
                } else if($entityObj[$columnName] == "UNI")  {
                    $prop->schemaName = 'unique';
                    $prop->schemaVal =  "true";
                    $prop->include = true;
                }  else if($entityObj[$columnName] == "MUL") {
                    $prop->include = false;
                }

                break;


             case "nullable":
                $prop->schemaVal =  $entityObj[$columnName] == "YES" ? "true" : "false";
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



    function getPureLength($string)
	{
		$sS = strrpos($string , '(');
        $sE = strrpos($string , ')');

        if($sS && $sE)
            return substr($string , $sS+1, ($sE-$sS)-1);

		return '';
	}


    /*
     * INSERT COLUMN
     *
     * @param   boolean      use verbose syntax
     */
    function insertMetadata($useVerboseSyntax=true)
    {

        $deliminator = '';
        $count = 0;
        $lines = array();

        $lines[] =  "\t/**";

        if($this->fields->columnKey->schemaName == "primary")
              $lines[] = "\t * @Id";

        $str = "\t * @Column(";
        $str .= "name=\"".$this->fields->name->schemaVal."\"";

        foreach($this->fields as $field => $val)
        {
            if(!empty($val->schemaVal) && $val->include == true)
            {
                if($count > 0 )
                    $deliminator = ', ';

                // append verbose length to type field e.g. varchar(255)
                switch($val->schemaName)
                {
                    case "type":
                        $str .= $deliminator.$val->schemaName."="."\"$val->schemaVal\"";
                        break;

                    default:
                        $str .= $deliminator.$val->schemaName."=".$val->schemaVal;
                        break;
                }
            }
            $count++;
        }
        $str .=  ")";
        $lines[] = $str;

        if($this->fields->autoincrement->schemaVal)
              $lines[] = "\t * @GeneratedValue(strategy=\"AUTO\")";

        $lines[] =  "\t */";
        return implode("\n", $lines);
    }

}

    /*
     * ============================================================================
     */
	function handleDoctrine2PHPBody($db, $table, $crlf)
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
         //   $lines[] = "detect_relations: true\n";
        }

        // build body

        $sqlQuery = "SELECT * FROM information_schema.columns WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table'";
		$result=PMA_DBI_query($sqlQuery);

        if ($result)
		{
			$tableProperties=array();
			while ($row = PMA_DBI_fetch_assoc($result))
            {
               $tableProperties[] = new TableProperty($row);
            }

             // insert table Class Headers
            $lines[] = "<?php\n";
			$lines[] = "namespace models;";
            $lines[] = "\n/**\n * @Entity\n * @Table(name=\"".$table."\")\n */\n";

            // insert class name
			$lines[] = "class ".toCamelCase( $table, true )." {\n\n\n";


			foreach ($tableProperties as $tablePropertie)
            {
                 //insert metadata
                 $lines[] = "    ".$tablePropertie->insertMetadata($useVerboseSyntax);

                 //insert property
                $propName = toCamelCase( $tablePropertie->fields->name->schemaVal );
                $lines[] = "	private $".$propName.";\n";
            }

			$lines[] = "\n";
			foreach ($tableProperties as $tablePropertie)
            {
                $functname = toCamelCase( $tablePropertie->fields->name->schemaVal, true );

                $lines[] = "	public function get".$functname."() \n\t{\n\t\treturn \$this->".$propName.";\n\t} \n";
                $lines[] = "	public function set".$functname."($".$propName.") \n\t{\n\t\t\$this->".$propName." = \$".$propName.";\n\t} \n";
            }

			$lines[] = "}\n?>\n\n";


			PMA_DBI_free_result($result);
		}


		return implode("\n", $lines);

	}

    function handleDoctrine2XMLBody($db, $table, $crlf)
	{

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
