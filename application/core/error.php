<?php
set_error_handler("errorHandler");
register_shutdown_function("shutdownHandler");
function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
{
	$error = "lvl: " . $error_level . " | msg:" . $error_message . " | file:" . $error_file . " | ln:" . $error_line;
	switch ($error_level) {
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_PARSE:
			_log($error, "Fatal error");
			break;
		case E_USER_ERROR:
		case E_RECOVERABLE_ERROR:
			_log($error, "Error");
			break;
		case E_WARNING:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
		case E_USER_WARNING:
			_log($error, "Warning");
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			_log($error, "Info");
			break;
		case E_STRICT:
			_log($error, "Debug");
			break;
		default:
			_log($error, "Warning");
	}
}

function shutdownHandler() //will be called when php script ends.
{
	$lasterror = error_get_last();
	switch ($lasterror['type'])
	{
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_USER_ERROR:
		case E_RECOVERABLE_ERROR:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
		case E_PARSE:
			$error = "[SHUTDOWN] date:".date('Y-m-d H:i:s')." lvl:" . $lasterror['type'] . " | msg:" . $lasterror['message'] . " | file:" . $lasterror['file'] . " | ln:" . $lasterror['line'];
			_log($error, "Fatal error");
	}
}
function _log($error, $errlvl){ 
	if($errlvl != 'Info')
		@file_put_contents(APPDIR. "/error.log", '['.$errlvl.'] '.$error."\r\n", FILE_APPEND);
}


/**
 * Syntax check PHP file
 *
 * @param string file path
 *
 * @return boolean checking result
 */
function syntax_check_php_file ($file) {
    // получим содержимое проверяемого файла
    @$code = trim(file_get_contents($file));
    
    // файл не найден
    if ($code === false) {
        throw new Exception('File '.$file.' does not exist');
    }
    if(substr($code, -2)!= '?>')  $code.='?>';
	
    // первый этап проверки
    $braces = 0;
    $inString = 0;
    foreach ( token_get_all($code) as $token ) {
        if ( is_array($token) ) {
            switch ($token[0]) {
                case T_CURLY_OPEN:
                case T_DOLLAR_OPEN_CURLY_BRACES:
                case T_START_HEREDOC: ++$inString; break;
                case T_END_HEREDOC:   --$inString; break;
            }
        }
        else if ($inString & 1) {
            switch ($token) {
                case '`':
                case '"': --$inString; break;
            }
        }
        else {
            switch ($token) {
                case '`':
                case '"': ++$inString; break;
 
                case '{': ++$braces; break;
                case '}':
                    if ($inString) {
                        --$inString;
                    }
                    else {
                        --$braces;
                        if ($braces < 0) {
                            throw new Exception('Braces problem!');
                        }
                    }
                break;
            }
        }
    }
     
    // расхождение в открывающих-закрывающих фигурных скобках
    if ($braces) {
        throw new Exception('Braces problem!');
    }
    
    $res = false;
     
    // второй этап проверки
    ob_start();
    $res = eval('if (0) {?>'.$code.'<?php }; return true;');
    $error_text = ob_get_clean();
     
    // устранение ошибки 500 в функции eval(), при директиве display_errors = off;
    header('HTTP/1.0 200 OK');
     
    if (!$res) {
        throw new Exception($error_text);
    } else {
		//$dir = dirname($file);file_put_contents($dir . "/log.log", $res);
	}
	
     
    return true;
}
