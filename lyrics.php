<?php
if($argc == 3)
{
$nl = "\n";
$artist = $argv[1];
$song = $argv[2];
}
else
{
  $nl = "<br />";
  $artist = $_REQUEST['artist'];
  $song = $_REQUEST['song'];
}
function ge($url)
{
  $ch = curl_init( $url );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
  curl_setopt( $ch, CURLOPT_HEADER, true );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_USERAGENT, "PHP55");
  $response = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ));
  $response = preg_split( '/([\r\n][\r\n]){2}/', curl_exec( $ch ),2);
  curl_close( $ch );
  return $response[1];
}
$dom = new DOMDocument();
@$dom->loadHTML(ge("http://www.azlyrics.com/lyrics/{$artist}/{$song}.html"));
$b = $dom->getElementsByTagName('b');
$header = $b->item(0);
$div = $header->nextSibling->nextSibling->nextSibling;
$innerHTML = "";
$children  = $div->childNodes;
foreach ($children as $child)
{
  $innerHTML .= $div->ownerDocument->saveHTML($child);
}
$result = str_replace("<br>",$nl,substr($innerHTML,10));
echo "Lyrics for: " . $header -> textContent . $nl;
echo $result;
