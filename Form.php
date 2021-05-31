<?php

class Form {
	private static $msg = null;
	private static $errors = array();
	private static $fileName = '';
	private static $newFilename = '';
	
	public static function getMessage() {
		return self::$msg;
	}
	
	public static function getErrors() {
		return self::$errors;
	}

	public static function getFileName() {
		return self::$fileName;
	}

	public static function getNewFileName() {
		return self::$newFilename;
	}

	public static function validateFile(string $key, $message, int $maxSize = 200000) {
		if (!empty($_FILES[$key]['tmp_name'])) {
		  $finfo = finfo_open(FILEINFO_MIME_TYPE);
		  $fileSize = $_FILES[$key]['size'];
			$tempFileName = $_FILES[$key]['tmp_name'];

		  $fileType = finfo_file($finfo, $tempFileName);
		  
			if (!in_array($fileType, ['image/png', 'image/jpeg', 'image/jpg'])) {
				self::$msg = "Загрузите данные в одном из графических форматов jpg, jpeg или png";
				return false;
			}

		  if ($fileSize > $maxSize) {
				self::$msg = "Превышен максимальный размер файла: " . $maxSize . "Кб";
				return false;
		  }
			self::$fileName = $_FILES[$key]['name'];

			$pointPos = strpos(self::$fileName, ".");
			$fileExt = substr(self::$fileName, $pointPos);
			//получим уникальное имя файла
			self::$newFilename = uniqid() . $fileExt;
			$uploadsPath = $_SERVER['DOCUMENT_ROOT'] . "/uploads" . '/';
			//переместим файл в папку uploads
			move_uploaded_file($tempFileName, $uploadsPath . self::$newFilename);
			return true;
		}	
		self::$msg = $message;
		return false;
	}

	public static function validateFields(array $rules, array $lot, array $messages) {
		$errors = array();
		foreach ($lot as $key => $value) {
			if (isset($rules[$key])) {
					$rule = $rules[$key];
					$errors[$key] = $rule($lot, $messages[$key]);
			}
		}
		self::$errors = array_filter($errors);
	}
}
