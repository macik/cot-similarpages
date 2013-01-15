<?php
/**
 * Similar Pages for Cotonti CMF
 *
 * @version 2.0.1
 * @author esclkm, http://www.littledev.ru
 * @copyright (c) 2008-2011 esclkm, http://www.littledev.ru
 *
 * // fixed for Siena 0.9.8 and up by Macik
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');
$db->query("ALTER TABLE {$db_x}pages ADD FULLTEXT(page_title)");

?>