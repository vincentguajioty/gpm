<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

function documentsGetIcone($ext)
{
    global $db;
    
	switch($ext)
	{
		// ------------------------------------ PDF et TXT
		case 'pdf':
			return "fa-file-pdf-o";
			break;
		case 'txt':
			return "fa-file-text-o";
			break;
		
		// ------------------------------------ WORD	
		case 'doc':
			return "fa-file-word-o";
			break;
		case 'docx':
			return "fa-file-word-o";
			break;
		
		// ------------------------------------ EXCEL
		case 'xls':
			return "fa-file-excel-o";
			break;
		case 'xlsx':
			return "fa-file-excel-o";
			break;
		case 'xlsm':
			return "fa-file-excel-o";
			break;
			
		// ------------------------------------ POWERPOINT	
		case 'ppt':
			return "fa-file-powerpoint-o";
			break;
		case 'pptx':
			return "fa-file-powerpoint-o";
			break;
		
		// ------------------------------------ ARCHIVES	
		case 'zip':
			return "fa-file-archive-o";
			break;
		case '7zip':
			return "fa-file-archive-o";
			break;
		case 'tar':
			return "fa-file-archive-o";
			break;
		case 'tgz':
			return "fa-file-archive-o";
			break;
		case 'rar':
			return "fa-file-archive-o";
			break;
		
		// ------------------------------------ AUDIO	
		case 'mp3':
			return "fa-file-audio-o";
			break;
		
		// ------------------------------------ IMAGES	
		case 'png':
			return "fa-file-image-o";
			break;
		case 'jpg':
			return "fa-file-image-o";
			break;
			
		// ------------------------------------ VIDEOS	
		case 'mp4':
			return "fa-file-movie-o";
			break;
		case 'mkv':
			return "fa-file-movie-o";
			break;
		
	
	
		default:
			return "fa-file-o";
			break;
	}
}
?>