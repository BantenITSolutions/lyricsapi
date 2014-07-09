<?php
error_reporting(E_ALL);
if(isset($argc))
{
  define("CLI",true);
  if($argc != 3) die("Usage: {$argv[0]} <artist> <song>\n");
  $nl = "\n";
  $artist = $argv[1];
  $song = $argv[2];
}
else
{
  define("CLI",false);
  $nl = "<br />\n";
  $artist = $_REQUEST['artist'];
  $song = $_REQUEST['song'];
}
$song = fix(strtolower($song));
$artist = fix(strtolower($artist));
$dom = new DOMDocument();
$html = ge("http://www.azlyrics.com/lyrics/{$artist}/{$song}.html");
@$dom->loadHTML($html);
$b = $dom->getElementsByTagName('b');
$header = $b->item(0);
$div = $header->nextSibling->nextSibling->nextSibling;
$innerHTML = "";
$children  = $div->childNodes;
foreach ($children as $child)
{
  $innerHTML .= $div->ownerDocument->saveHTML($child);
}
$result = str_replace("’","'",str_replace("<br>",$nl,substr($innerHTML,9)));
if(!CLI) echo "<!DOCTYPE html>\n<html>\n<head>\n<title>" . $dom->getElementsByTagName('title')->item(0)->textContent . "</title>\n</head>\n<body>\n";
echo "Lyrics for: " . $header -> textContent . $nl;
echo $result;
if(!CLI) echo "\n</body>\n</html>";
function fix($a)
{
  return str_replace(" ","",str_replace(".","",$a));
}
function ge($url)
{
  $cdir = CLI ? $_SERVER['HOME'] . DIRECTORY_SEPARATOR . ".cache" : "cache";
  if(!is_dir($cdir)) mkdir($cdir);
  $cachename = $cdir . DIRECTORY_SEPARATOR . str_replace("/","-",$url);
  if(file_exists($cachename)) return file_get_contents($cachename);
  $ch = curl_init( $url );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $ch, CURLOPT_HEADER, true );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:32.0) Gecko/20100101 Firefox/32.0");
  $a = curl_exec( $ch );
  $response = preg_split( '/([\r\n][\r\n])\\1/', $a);
  $response = preg_split( '/([\r\n][\r\n]){2}/', $a,2);
  curl_close( $ch );
  file_put_contents($cachename,$response[1]);
  return $response[1];
}
if(CLI) echo "\n";
