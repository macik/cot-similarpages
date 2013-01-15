<?php

/**
 * [BEGIN_COT_EXT]
 * Hooks=page.tags,ajax
 * Tags=page.tpl:{PLUGIN_SIMILARPAGES}
 * [END_COT_EXT]
 */

/**
 * Similar Pages for Cotonti CMF
 *
 * @version 2.0.0
 * @author esclkm, http://www.littledev.ru
 * @copyright (c) 2008-2011 esclkm, http://www.littledev.ru
 */
defined('COT_CODE') or die('Wrong URL.');

require_once(cot_langfile('similarpages'));
require_once(cot_incfile('page', 'module'));
require_once(cot_incfile('users', 'module'));

$relev = $cfg['plugin']['similarpages']['relev'];
$limit = $cfg['plugin']['similarpages']['limit'];


if (COT_AJAX)
{
	$id = cot_import('id', 'G', 'INT');
	$sql = $db->query("SELECT page_id, page_title, page_cat FROM $db_pages WHERE page_id='$id'");
	$pag = $sql->fetch();
}

$catsub = array();
$catsub = cot_structure_children('page', $pag['page_cat']);
if (count($catsub) > 0 && $cfg['plugin']['similarpages']['catcontrol'])
{
	$sqladd = " AND page_cat IN ('".implode("','", $catsub)."')";
}

$sim_p = new XTemplate(cot_tplfile('similarpages', 'plug'));
$similartext = $db->prep($pag['page_title']);

$sql_sim = $db->query("SELECT p.*, u.* FROM $db_pages AS p LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid WHERE  (p.page_state='0' OR p.page_state='2') AND p.page_id != ".$pag['page_id']." $sqladd AND MATCH (page_title) AGAINST ('$similartext')>$relev LIMIT $limit");
$jj = 0;
while ($pag2 = $sql_sim->fetch())
{
	$jj++;
	$sim_p->assign(cot_generate_pagetags($pag2, 'PAGE_ROW_'));
	$sim_p->assign(array(
		"PAGE_ROW_ODDEVEN" => cot_build_oddeven($jj),
		"PAGE_ROW_NUM" => $jj,
	));

	$sim_p->parse("SIMILARPAGES.SIMILAR_LIST.PAGE_ROW");
}
if ($jj < 1)
{
	$sim_p->parse("SIMILARPAGES.NOSIMILAR_LIST");
}
else
{
	$sim_p->parse("SIMILARPAGES.SIMILAR_LIST");
}

$sim_p->parse("SIMILARPAGES");
if (COT_AJAX)
{
	cot_sendheaders();
	$sim_p->out("SIMILARPAGES");
}
else
{
	$pop_text = $sim_p->text("SIMILARPAGES");
	$t->assign("PLUGIN_SIMILARPAGES", $pop_text);
}
?>
