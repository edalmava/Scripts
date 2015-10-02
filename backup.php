<?php
date_default_timezone_set("America/Bogota");

class Tiempo {
	private $tiempoInicial;
	private $tiempoFinal;
	
	function __construct() {
		$this->tiempoInicial = time();
	}
	
	function setTiempoFinal() {
		$this->tiempoFinal = time();
	}
	
	function getTiempoSeg() {
		return $this->tiempoFinal - $this->tiempoInicial;
	}
	
	function getTiempoMin() {
		return ($this->getTiempoSeg()/60);
	}
}

class Backup {
	private $rutaDirectorio;
	private $rutaPgDump;
	
	function __construct($rutaDirectorio, $rutaPgDump) {
		$this->rutaDirectorio  = $rutaDirectorio;
		$this->rutaPgDump      = $rutaPgDump;
		if (!$this->crearDirectorio($rutaDirectorio)) {
			throw new Exception("No se puede crear el directorio: {$rutaDirectorio}");
		}
	}
	
	function crearDirectorio($directorio) {
		if (!is_dir($directorio)) {
			if (!mkdir($directorio)) {
				return false;
			}
		} 
		return true;
	}
	
	function dump($institucion) {
		$rutaArchivo = $this->rutaDirectorio . '/' . $institucion . '_' . date('dmYHis') . '.backup';
	    $comando = '"' . $this->rutaPgDump . '" --host 192.168.1.1 --port 5432 --username "edalmava" --no-password  --format custom --blobs --file "' . $rutaArchivo . '" "' . $institucion . '"';
		system($comando, $retVal);
		return $retVal;
	}
	
	function dumpInstituciones($instituciones) {
		foreach($instituciones as $institucion) {
			echo "$institucion: ";		
			echo (!$this->dump($institucion))?"[OK]\n":"[ERROR]\n";		
		}
	}
}

$instituciones = array('colegio1', 'colegio2', 'colegio3');

echo "************************************************************\n";
echo "*     Programa para Backup de base de datos PostgreSQL     *\n";
echo "************************************************************\n";
echo "\n\n";

try {
	$backup = new Backup('D:/BD/' . date('dmY'), 'C:/Program Files/PostgreSQL/9.4/bin\pg_dump.exe');
	$tiempo = new Tiempo();
	
	$backup->dumpInstituciones($instituciones);
	
	$tiempo->setTiempoFinal();

	echo "\n\n";
	echo "Tiempo empleado: ({$tiempo->getTiempoSeg()}seg) ({$tiempo->getTiempoMin()}min)";
} catch(Exception $e) {
	echo "ERROR: " . $e->getMessage() . "\n";
}