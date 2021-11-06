<?php
if(isset($_POST['SEND']))
{
    $debug = "0"; //Set to 1 to debug array
    $config = array(
        'url' => 'https://atendimento.semedero.org/api/tickets.json',  //URL to site.tld/api/tickets.json
        'key' => 'FBC4EB1A542917319E6C1DA87950ABAB'  //API key osTicket
    );

    $message = 'Desk: ' . $_POST['desk'] . "\r\n\r\n" . $_POST['message']; //Concatenate select and input text from form
	
    $data = array(
        'alert' => 'false', //Alert to staff
        'autorespond' => 'false', //Autoreponse from osTicket installation
        'source' => 'API', //Static source of ticket
        'name' => $_POST['nome'], //Name
        'email' => $_POST['email'], //Emailadress
        'phone' => $_POST['celular'], //Phone number
        'subject' => 'Teste de envio', //Ticket title
        'ip' => $_SERVER['REMOTE_ADDR'], //IP adress of machine sending ticket 
		'message' => 'Teste de Mensagem', //Plain text
        'attachments' => array(),
    );
	
	$target_dir="";
	$filename="";
	
	$totalattachments = count($_FILES['attachments']['name']); //Count number of attachments
	
	for($i=0; $i<$totalattachments; $i++) { //Start loop of encoding attachments based on number of attachments
		if(!$_FILES["attachments"]["error"][$i] == 4){ //4 means no file uploaded
			$target_dir=$_SERVER['DOCUMENT_ROOT']."/osticket/upload/attachments/"; //Get directory of file
			$target_dir = $target_dir . basename($_FILES["attachments"]["name"][$i]); //Get absolute path to file

			$filename=$_FILES["attachments"]["name"][$i]; //Get file name

			if (move_uploaded_file($_FILES["attachments"]["tmp_name"][$i], $target_dir)) { //Get temporary location and move to target directory
				chmod($target_dir, 0777); 
			} else {
				echo "Sorry, there was an error uploading your file.";
			}
			
			$type = pathinfo($target_dir, PATHINFO_EXTENSION); //Get file extension type
			
			$mime='data:text/plain;'; //Default mime (txt)
			
			$imgext=array("jpg","jpeg","gif","png","bmp");
			$excelext=array("xlsx","xls");
			$pdfext=array("pdf");
			$docext=array("doc","docx");

			if(in_array($type, $imgext)){
				$mime="data:image/".$type.";";   
			}
			
			if(in_array($type, $pdfext)){
				$mime="data:application/".$type.";";      
			}
			
			if(in_array($type, $excelext)){
				if($type=="xlsx")
					$mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;";
				else
					$mime="application/msword;";    
			}
			
			if(in_array($type, $docext)){
				if($type=="docx")
					$mime="application/vnd.openxmlformats-officedocument.wordprocessingml.document;";
				else
					$mime="application/msword;";
			}
			
			$data['attachments'][] = array($filename => $mime.'base64,'.base64_encode(file_get_contents($target_dir))); //Add attachment to array
		}
	}
	
    if ($debug == '1') {
		print_r($data);
        die();
    }

    #pre-checks
    function_exists('curl_version') or die('CURL support required');
    function_exists('json_encode') or die('JSON support required');
	
    #set timeout
    set_time_limit(30);
	
    #curl post
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['url']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.8');
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'X-API-Key: ' . $config['key']));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch); // Ticket number
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //HTTP Code
    curl_close($ch);
	
    if ($code != 201)
        die("<script type='text/javascript'>alert('Unable to create ticket: '.$result')</script>");
    $ticket_id = (int)$result;
	
	print_r('Ticket number: #' . $ticket_id); //Print ticket number on page

    if (isset($ticket_id) && $ticket_id != '') {
        echo "<script type='text/javascript'>alert('Conseguimos');</script>";
    } else {
        echo "<script type='text/javascript'>alert('Não foi dessa vez.');</script>";
    }
}
?>