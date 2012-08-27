<?php

/***************************************************************/
/* includes                                                    */
/***************************************************************/

/***************************************************************/
/* defines                                                     */
/***************************************************************/

/***************************************************************/
/* global                                                      */
/***************************************************************/

// dummy comment
class Link
{
	public $name;
	public $path;
	
	public function __construct($name, $path)
	{
		$this->name = $name;
		$this->path = $path;
	}
}

function getDirs($root)
{
	$list = array();
	$dirs = \scandir($root);

	foreach ($dirs as $d)
	{
		if (!\is_dir($root .'/' . $d) || $d === '.svn' || $d === '.' || $d === '..')
			continue;
		
		$list[] = $d;
	}
	
	\sort($list);
	
	return $list;
}

$sb = \getenv('SBNAME');
$root = $sb . '\\project';

if (!\is_dir($root))
{
	print 'Invalid sb=' . $sb;
	exit;
}

$dirs = \getDirs($root);
$projects = array();

foreach ($dirs as $d)
{
	if (\is_dir($root . '/' . $d . '/www'))
		$projects[$d] = array(new Link('www', ''));
}

$projects['Cv'][] = new Link('admin', 'admin');
$projects['Cv'][] = new Link('thesis', 'thesis');
$projects['Cv'][] = new Link('thesis/admin', 'thesis/admin');
$projects['Kdo'][] = new Link('admin', 'admin');
$projects['SrcCode'] = array(
	new Link('cyclic', 'cyclic.php'),
	new Link('libs', 'libs.php'),
	new Link('search', 'search.php?recursive=on&lbl=on&case=on'),
	new Link('search/php', 'search.php?rule00_patterns=.svn&rule01_patterns=*.php%20*.js&rule01_incl=on&rule01_file=on&recursive=on&lbl=on&case=on'),
	new Link('search/cpp', 'search.php?rule00_patterns=.svn&rule01_patterns=*.cpp%20*.c%20*.h&rule01_incl=on&rule01_file=on&recursive=on&lbl=on&case=on&dir=S:\\SB_'));
$projects['SteveHome'][] = new Link('config', 'admin/config/FileList.php');
$projects['SteveHome'][] = new Link('admin', 'admin');
$projects['SteveHome'][] = new Link('batch', 'admin/batch.php');

$localOnly = array('Deploy', 'UnitTest', 'Boda');

$html_table = '<table>';

foreach ($projects as $name => $links)
{
	$html_table .= '<tr>';
	$html_table .= '<td>' . $name . '</td>';
	$html_table .= '<td><ul>';
	
	foreach ($links as $link)
	{
		$html_links = '';
		$href = $root . '/' . $name . '/www/' . $link->path;
		$html_links .= '<li><a href="' . $href . '">' . $link->name . '</a></li>';
		
		if (!\in_array($name, $localOnly))
		{
			$href = 'https://www.stevehome.com/' . ($name === 'SteveHome' ? '' : 'misc/' . $name . '/') . $link->path;
			$html_links .= '<li><a href="' . $href . '">' . $link->name . '</a></li>';
		}
		
		$html_table .= '<li><ul>' . $html_links . '</ul></li>';
	}
	
	$html_table .= '</ul></td>';
	$html_table .= '</tr>';
}

$html_table .= '</table>';

$env = array(
	'SBNAME',
	'LOC',
	'DB',
	'STEVEHOME_ROOT',
	'STEVEHOME_PATH_LOCAL',
	'STEVEHOME_ENV',
	'STEVEHOME_WEBSERVER_SLASHEDHTTPVARS');

$html_table_env = '<table>';

foreach ($env as $e)
{
	$html_table_env .= '<tr>'
		. '<td>' . $e . '</td>'
		. '<td>' . \getenv($e) . '</td>'
		. '</tr>';
}

$html = '<html>'
	. '<head>'
		. '<title>' . $sb . '</title>'
		. '<style>'
			. 'body {font-family: Arial; font-size: 70%;}'
			. 'table {border: 1px solid black; margin: 1em; font-size: 100%}'
			. 'tr {vertical-align: top;}'
			. 'tr > td:first-child {background-color: #eeeeee;}'
			. 'ul {list-style-type: none; padding: 0em; margin: 0em;}'
			. 'td > ul > li {display: inline; float: left;}'
			. 'td > ul > li > ul > li {padding: 0.2em 1em;}'
			. 'td > ul > li > ul > li:nth-child(2) {background-color: #ffff99;}'
		. '</style>'
	. '</head>'
	. '<body>'
	. '<a href="copyscripts.php?sandbox=' . $sb . '">copy scripts</a>'
	. $html_table
	. $html_table_env
	. '</body>'
	. '</html>';

print $html;


?>
