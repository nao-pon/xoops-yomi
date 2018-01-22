<?php
// ------------------------------------------------------------------------- //
//                XOOPS - PHP Content Management System                      //
//                       <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------- //
// Based on:                                                                 //
// myPHPNUKE Web Portal System - http://myphpnuke.com/                       //
// PHP-NUKE Web Portal System - http://phpnuke.org/                          //
// Thatware - http://thatware.org/                                           //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //

include("../../mainfile.php");
include("./include/hyp_tickets.php");
define('YOMI_TICKET_TAG',$xoopsHypTicket->getTicketHtml( __LINE__ ));

$xoopsOption['show_rblock'] =0;
include(XOOPS_ROOT_PATH."/header.php");
//echo "now debuging!";exit;
//OpenTable();
//include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");

global $xoopsUser,$xoopsDB,$xoopsTpl;

if (is_object($xoopsTpl)) $xoopsTpl->assign('ads_shown',1);

$is_admin = 0;
$x_uid = 0;
if ( $xoopsUser ) {
	$module_handler = xoops_gethandler('module');
	$xoopsModule = $module_handler->getByDirname('yomi');
	if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) { 
		$is_admin = 1;
	}
	$x_uid = $xoopsUser->uid();
}

//
if (isset($_GET['id'])) $_GET['id'] = intval($_GET['id']);
if (isset($_GET['kt'])) $_GET['kt'] = preg_replace("/[^0-9_]+/","",$_GET['kt']);

if (isset($_POST['id'])) $_POST['id'] = intval($_POST['id']);
if (isset($_POST['kt'])) $_POST['kt'] = preg_replace("/[^0-9_]+/","",$_POST['kt']);

echo "<table class=\"yomi-body\"><tr><td>";

?>