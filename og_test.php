<?
include('../include/debug.php');
require_once('OpenGraph.php');

$graph = OpenGraph::fetch('http://grassboy.tw/datapic/');
$result = array();
foreach ($graph as $key => $value) {
  $result[$key] = $value;
}
exit_r($result);
?>
