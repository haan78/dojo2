<?php
class Upload {
	private $maximumSize;
	private $forbiddenPatterns;
	private $lastError;
	private $allowedExts;

	public function __construct() {
		$this->maximumSize = 100 * 1024 * 1024;
		$this->forbiddenPatterns = array("htaccess","\.php");
		$this->lastError = false;
		$this->allowedExts = array();
	}

	public function getLastError() {
		return $this->lastError;
	}

	public function noForbiddenPattern() {
		$this->forbiddenPatterns = array();
	}

	public function addForbiddenPattern($reg) {
		array_push($this->forbiddenPatterns,$reg);
	}

	public function addAllowedExtension($ext) {
		if ( !is_string($ext) ) {
			throw new Exception("Extension must be string");
		}
		array_push($this->allowedExts,$ext);
	}

	public function loadAllowedExtensions($arr) {
		$this->allowedExts = array();
		for ($i=0; $i<count($arr); $i++ ) {
			$this->addAllowedExtension($arr[$i]);
		}
	}

	private function isAllowed($file) {
		if ( count($this->allowedExts) > 0 ) {
			for ( $i=0; $i<count($this->allowedExts); $i++ ) {
				$info = pathinfo($file);
				$ext = strtolower($info['extension']);
				if ( $ext == strtolower($this->allowedExts[$i]) ) {                                    
					return true;
				}
			}
			return false;
		} else {
			return true;
		}
	}

	private function isForbidden($file) {
		for ($i=0; $i<count($this->forbiddenPatterns); $i++) {
			$matches = null;
			if (preg_match("/".$this->forbiddenPatterns[$i]."/si",$file,$matches)) {
				return true;
			}
		}
		return false;
	}


	public function setMaximumSize($size) {
		$this->maximumSize = intval($size);
	}

	public function save($name,$folder,$guid = false) {
		if ( isset( $_FILES[$name] ) ) {
			if ( !$this->isForbidden( basename($_FILES[$name]['name']) ) ) {
				if ( $this->isAllowed($_FILES[$name]['name']) ) {
					if ( $_FILES[$name]["size"] <= $this->maximumSize ) {
						if ( file_exists($folder) ) {
							if ( is_writable($folder) ) {
								$fn = basename($_FILES[$name]['name']);
								if ( $guid !==false ) {
									$info = pathinfo($_FILES[$name]['name']);
									$fn = uniqid($guid).".".$info['extension'];
								}
								if (move_uploaded_file($_FILES[$name]['tmp_name'], $folder."/".$fn)) {
									$this->lastError = false;
									return $fn;
								} else {
									$this->lastError = "File could not save";
								}
							} else {
								$this->lastError = "Folder is not writable";
							}
						} else {
							$this->lastError = "Folder is not exist";
						}
					} else {
						$this->lastError = "File is too big";
					}
				} else {
					$this->lastError = "File extension is not allowed";
				}
			} else {
				$this->lastError = "File pattern is forbidden";
			}
		} else {
			$this->lastError = "Upload name is not in files";
		}
		return false;
	}
}