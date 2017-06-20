<?php
/**
 * @package    EvalforGescompevalBundle
 * @copyright  2010 onwards EVALfor Research Group {@link http://evalfor.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Daniel Cabeza Sánchez <daniel.cabeza@uca.es>
 */
namespace Evalfor\GescompevalBundle\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class csvClass
{
	/**
     * CSV file
     *
     * @var \SplFileObject
     */
	protected $file;
	
	/**
	 * CSV filename
	 *
	 * @var string
	 */
	protected $filename;
	
	/**
	 * Column headers as read from the CSV file
	 *
	 * @var array
	 */
	protected $columnHeaders = array();
	
	/**
	 * Number of column headers, stored and re-used for performance	 
	 *
	 * @var integer
	*/
	protected $headersCount;
	
	/**
	 * rows in the CSV file
	 *
	 * @var array
	 */
	protected $rows = array();
	
	/**
	 * Total number of rows in the CSV file
	 *
	 * @var integer
	 */
	protected $count;
	
	/**
	 * Faulty CSV rows
	 *
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * Indicates if CSV file has got any critical error
	 * @var bool
	 */
	protected $hasGeneralErrors = false;
	
	/**
	 * Strict parsing - skip any lines mismatching header length
	 *
	 * @var boolean
	*/
	protected $strict = true;
		
	/**
	 * Delimiter of CSV file
	 *
	 * @var string
	 */
	protected $delimiter;
	
	/**
	 * Enclosure of CSV file
	 *
	 * @var string
	 */
	protected $enclosure;
	
	/**
	 * Escape of CSV file
	 *
	 * @var string
	 */
	protected $escape;
	
	
	
	/**
	 * @param UploadedFile	 $file
	 * @param string         $delimiter
	 * @param string         $enclosure
	 * @param string         $escape
	 */
	public function __construct($file, $delimiter = ';', $enclosure = '"', $escape = '\\', $uniqueColumns = array())
	{
		ini_set('auto_detect_line_endings', true);
		$this->filename = $file->getRealPath();
		$this->delimiter = $delimiter;
		$this->enclosure = $enclosure;
		$this->escape = $escape;
		
		$this->file = new \SplFileObject($this->filename);
		
		$this->file->setFlags(
				\SplFileObject::READ_CSV |
				\SplFileObject::SKIP_EMPTY |
				\SplFileObject::READ_AHEAD |
				\SplFileObject::DROP_NEW_LINE
		);
		$this->file->setCsvControl(
				$delimiter,
				$enclosure,
				$escape
		);
		
		$this->errors['generalErrors'] = array();
		$this->errors['invalidRows'] = array();
		
		//Check extension
		$extensionFile = $file->getClientOriginalExtension();
		if(strtolower($extensionFile) != 'csv'){
			array_push($this->errors['generalErrors'], 'An error occurred while loading the CSV file: Invalid extension: It must be .csv');
		}
		
		//Check Mime Type
		$mimeType = $file->getMimeType();
		if($mimeType != "text/plain"){
			array_push($this->errors['generalErrors'], 'An error occurred while loading the CSV file: Invalid MIME type: It must be text/plain');
		}
		
		//Check File Size
		$fileSize = $this->file->getSize();
		$maxFileSize = UploadedFile::getMaxFilesize();
		if($fileSize > $maxFileSize){
			array_push($this->errors['generalErrors'], 'An error occurred while loading the CSV file: The file size exceeds supported by the server.');
		}
		
		/* 
		 * TODO: Reemplazar caracteres Mac y Windows de saltos de línea y retornos de carro
		 * $content = $this->file->fread($this->file->getSize());
		 * $content = preg_replace('!\r\n?!', "\n", $content);
		 * $tempfile = tempnam(make_temp_directory('/csvimport'), 'tmp');
		 * if (!$fp = fopen($tempfile, 'w+b')) {
         * 		$this->_error = get_string('cannotsavedata', 'error');
         * 		  @unlink($tempfile);
         *		   return false;
         * }
         * fwrite($fp, $content);
         * fseek($fp, 0);
		 */
		if(empty($this->errors['generalErrors'])){
			$this->columnHeaders = $this->readHeader();
			$this->rows = $this->readContent();
		}
		else{
			$this->hasCritialErrors = true;
		}
	
	}
    
    /**
     * Read columns of the header
     * @return array:
     */
    private function readHeader(){
    	$row = $this->getRow();
    	
    	//Check duplicated columns
    	$auxArray = array();
    	foreach($row as $key => $value){
    		if(in_array($value, $auxArray)){
    			array_push($this->errors['generalErrors'], 'An error occurred while loading the CSV file: CSV format file invalid: Duplicated field name "'. $value .'"');
    		}
    		$auxArray[$key] = $value;
    		
    		//If file is encoded with UTF-8 with BOM, it will remove Unicode BOM from first line
    		$row[$key] = trim(self::trim_utf8_bom($value));
    	}
    	
    	$this->headersCount = count($row);
  
    	return $row;
    }
    
    /**
     * Read content of the file
     * @return array of rows:
     */
    private function readContent(){
    	$rows = array();
    	while($row = $this->getRow()){
    		if(count($row) == $this->headersCount){
    			array_push($rows,$row);
    		}
    		else{
    			array_push($this->errors['generalErrors'], 'An error occurred while loading the CSV file: CSV format file invalid: the number of columns is not constant.');
    			break;
    		}
    	}
    	$this->count = count($rows);
    	return $rows;
    }
    
    /**
     * Return a row
     *
     * @return array or false
     */
    private function getRow()
    {
    	if (($row = $this->file->fgetcsv()) !== false) {
    		//Check Encoding
    		if(is_array($row)){
    			foreach($row as $key => $value){
    				if(mb_detect_encoding($value, array('UTF-8','ISO-8859-15')) != 'UTF-8'){ 
    					$value = utf8_encode($value);
    					$row[$key] = $value;
    				}
    				
	    			if(!mb_check_encoding($value, 'UTF-8')){
	    				$message = 'An error occurred while loading the CSV file: Invalid encoding: The file must be encoded in UTF-8';
	    				if(!in_array($message, $this->errors['generalErrors'])){
    						array_push($this->errors['generalErrors'], $message);
	    				}
	    				$this->hasCritialErrors = true;
    				}
    			}
    		}
    		
    		if(!empty($this->columnHeaders) && is_array($row) && count($row) == $this->headersCount){
    			$result = array_combine($this->columnHeaders, $row);
    			return $result;
    		}
    		else{
    			return $row;
    		}
    	} else {
    		return false;
    	}
    }
    
    public static function trim_utf8_bom($str) {
    	$bom = "\xef\xbb\xbf";
    	if (strpos($str, $bom) === 0) {
    		return substr($str, strlen($bom));
    	}
    	return $str;
	}
    
    /**
     * Close file
     */
    public function __destruct()
    {
    	if (is_resource($this->file)) {
    		fclose($this->file);
    	}
    }
    
    /**
     * Return rows of CSV file
     * @return array
     */
    public function getRows(){
    	return $this->rows;
    }
    
    /**
     * Return all errors in CSV file
     * @return array;
     */
    public function getErrors(){
    	return $this->errors;
    }
    
    /**
     * Indicates if CSV file has got critical errors
     * @return boolean
     */
    public function hasGeneralErrors(){
    	return $this->hasGeneralErrors;
    }
    
    /**
     * Return general errors in CSV file
     * @return array;
     */
    public function getGeneralErrors(){
    	return $this->errors['generalErrors'];
    }
    
    /**
     * Return invalid rows in CSV file
     * @return array;
     */
    public function getInvalidRows(){
    	return $this->errors['invalidRows'];
    }  
    
    public function getColumnHeaders(){
    	return $this->columnHeaders;
    }
    
    public function getHeadersCount(){
    	return $this->headersCount;
    }
    
}
?>