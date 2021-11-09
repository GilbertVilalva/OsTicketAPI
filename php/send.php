<?php
 
#
 
# Configuration: Enter the url and key. That is it.
 
# url = URL to api/task/cron e.g # http://yourdomain.com/support/api/tickets.json
 
# key => API's Key (see admin panel on how to generate a key)
 
# $data add custom required fields to the array.
 
#
 
# Originally authored by jared@osTicket.com
 
# Modified by ntozier@osTicket / tmib.net
 
# Attachments fixed by ddhnote@gmail.com
 
// You must configure the url and key in the array below.
 
$config = array(
 
'url'=>'https://atendimento.semedero.org/api/tickets.json', // URL to site.tld/api/tickets.json
 
'key'=>'FBC4EB1A542917319E6C1DA87950ABAB' // API Key goes here
 
);
 
# NOTE: some people have reported having to use "http://your.domain.tld/api/http.php/tickets.json" instead.
 
if($config === 'http://your.domain.tld/api/tickets.json') {
 
echo "Error: No URLYou have not configured this script with your URL!
";
 
echo "Please edit this file ".__FILE__." and add your URL at line 18.
 
";
 
die();
 
}
 
if(IsNullOrEmptyString($config['key']) || ($config === 'PUTyourAPIkeyHERE')) {
 
echo "Error: No API KeyYou have not configured this script with an API Key!
";
 
echo "
 
Please log into osticket as an admin and navigate to: Admin panel -> Manage -> Api Keys then add a new API Key.";
 
echo "Once you have your key edit this file ".__FILE__." and add the key at line 19.
";
 
die();
 
}
 
# Fill in the data for the new ticket, this will likely come from $_POST.
 
# NOTE: your variable names in osT are case sensiTive.
 
# So when adding custom lists or fields make sure you use the same case
 
# For examples on how to do that see Agency and Site below.
 
$data = array(
 
  'priority'  => 'false',
  'alert'     => 'true',
  'name'      => 'GilTeste',  // from name aka User/Client Name
  'email'     => 'gil@gil.com',  // from email aka User/Client Email
  'phone'     => '22997601726', // phone number aka User/Client Phone Number
  'subject'   => 'Test API message',  // test subject, aka Issue Summary
  'message'   => 'This is a test of the osTicket API',  // test ticket body, aka Issue Details.
  'ip'        => $_SERVER['REMOTE_ADDR'], // Should be IP address of the machine thats trying to open the ticket.
  'topicId'   => '23', // the help Topic that you want to use for the ticket
  //'assignId'  => 't11',
  //'deptId'    => '7',
  'escolas'   => '11',
 
/*'attachments' => array(
array('diario.jpg' => 'data:image/jpg;base64,'.base64_encode(file_get_contents("diario.jpg"))),
array('wallpaper.jpg' => 'data:image/jpg;base64,'.base64_encode(file_get_contents("wallpaper_ubuntu1.jpg"))),
array('comprovante.pdf' => 'data:application/pdf;base64,'.base64_encode(file_get_contents("comprovante.pdf"))),
array(
 


 
'file.txt' => 'data:text/plain;base64,'.base64_encode(file_get_contents('file.txt'))

 
)
 
)*/ );
 
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
 
curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$config['key']));
 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
 
$result=curl_exec($ch);
 
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
curl_close($ch);
 
if ($code != 201)
 
die('Unable to create ticket: '.$result);
 
$ticket_id = (int) $result;
print $ticket_id;
 
# Continue onward here if necessary. $ticket_id has the ID number of the
 
# newly-created ticket
 
function IsNullOrEmptyString($question){
 
return (!isset($question) || trim($question)==='');
 
}
 
?>