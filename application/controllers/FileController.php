<?php

/**
 * Controlador para los archivos que se manejaran en el sistema (datasheet, basisfile)
 * @author maque
 */
namespace Controllers;

class FileController extends \Controllers\Controller{
	
	/**
	 * Buscar el file
	 * @param  IdFile
	 * @return object \Models\File
	 */
  	public function getFile($params){
	  
	  	$file = \Models\File::findById($params);
	  	return $file;
	  }	

	 public  function  addFile($content, $fileName, $fileSize, $fileType){
		
			$addFile= new \Models\File();
			$addFile->setArchivo($content);
			$addFile->setName($fileName);
			$addFile->setSize($fileSize);
			$addFile->setType($fileType);
				
			$response = $addFile->saveOrUpdate();
					
			if ($response === true || $response == 1) {
				
			return $addFile;
		
			}	
			else {
				return $response;
				
				
			}
		}
}
?>			
			
				
								
			

