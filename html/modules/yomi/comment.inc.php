<?php
// ------------------------------------------------------------------------- //
//                XOOPS - PHP Content Management System                      //
//                       <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------- //
// Based on:								     //
// myPHPNUKE Web Portal System - http://myphpnuke.com/	  		     //
// PHP-NUKE Web Portal System - http://phpnuke.org/	  		     //
// Thatware - http://thatware.org/					     //
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
//include("header.php");
$myts =& MyTextSanitizer::getInstance();// MyTextSanitizer object
include_once(XOOPS_ROOT_PATH."/class/xoopstree.php");
include_once(XOOPS_ROOT_PATH."/class/xoopscomments.php");
//for XOOPS 2
if (file_exists(XOOPS_ROOT_PATH."/include/old_functions.php"))
	include_once(XOOPS_ROOT_PATH."/include/old_functions.php");
//--------------//
$lid = $_GET['item_id'];
//include(XOOPS_ROOT_PATH."/header.php");
OpenTable();
//mainheader();

if ($lid == "") {
	$lid = $item_id;
}
if ($item_id == "") {
	$item_id = $lid;
}

// comments

//include(XOOPS_ROOT_PATH."/header.php");
// set comment mode if not set
if ( !isset($mode) || $mode == "" || ($mode != "nocomments" && $mode != "thread" && $mode != "flat") ) {
	if ( $xoopsUser ) {
		$mode = $xoopsUser->getVar("umode");
	} else {
		$mode = $xoopsConfig['com_mode'];
	}
}
if ($mode != "nocomments" && $mode != "thread" && $mode != "flat") $mode = "flat";
// set comment order if not set
if ( !isset($order) ) {
	if ( $xoopsUser ) {
		$order = $xoopsUser->getVar("uorder");
	} else {
		$order = $xoopsConfig['com_order'];
	}
}
if ( !empty($comment_id) ){
	$yomicomment = new XoopsComments($xoopsDB->prefix("yomi_comments"),$comment_id);
} else {
	$yomicomment = new XoopsComments($xoopsDB->prefix("yomi_comments"));
}

$orderby = ($order == 1) ? "date DESC" : "date ASC";
if ( $mode == "flat" ) {
	$criteria = array("item_id=".$lid."");
	$commentsArray = $yomicomment->getAllComments($criteria, true, $orderby);
} elseif ( $mode == "thread" ) {
	$criteria = array("item_id=".$lid."", "pid=".$yomicomment->getVar("pid")."");
	$commentsArray = $yomicomment->getAllComments($criteria, true, $orderby);
} else {
	$commentsArray = "";
}
$yomicomment->printNavBar($item_id, $mode, $order);
// Now, show comments
if ( is_array($commentsArray) && count($commentsArray) ) {
	if ( $xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid()) ){
		$adminview = 1;
	} else {
		$adminview = 0;
	}
	if ( $mode == "flat" ) {
		$yomicomment->showThreadHead();
		$count = 0;
		foreach ( $commentsArray as $ele ) {
			if ( !($count % 2) ) {
				$color_num = 1;
			} else {
				$color_num = 2;
			}
			$ele->showThreadPost($order, $mode, $adminview, $color_num);
			$count++;
		}
		$yomicomment->showThreadFoot();
	}
	if ( $mode == "thread" ) {
		foreach ( $commentsArray as $ele ) {
			$ele->showThreadHead();
			$ele->showThreadPost($order, $mode, $adminview);
			$ele->showThreadFoot();
			//show thread tree
			//if not in the top page, show links to parent and top comment
			if ( $ele->getVar("pid") != 0 ) {
				echo "<div style='text-align:left'>";
				echo "&nbsp;<a href='".$PHP_SELF."?item_id=".$ele->getVar("item_id")."&amp;mode=".$mode."&amp;order=".$order."'>"._TOP."</a>&nbsp;|&nbsp;";
				echo "<a href='".$PHP_SELF."?item_id=".$ele->getVar("item_id")."&amp;comment_id=".$ele->getVar("pid")."&amp;mode=".$mode."&amp;order=".$order."#".$ele->getVar("pid")."'>"._PARENT."</a>";
				echo "</div>";
			}
			echo "<br />";
			$treeArray = $ele->getCommentTree();
			if ( count($treeArray) >0 ) {
				$ele->showTreeHead();
				$count = 0;
				foreach ( $treeArray as $treeItem ) {
					if ( !($count % 2) ) {
						$color_num = 1;
					} else {
						$color_num = 2;
					}
					$treeItem->showTreeItem($order, $mode, $color_num);
					$count++;
				}
				$ele->showTreeFoot();
			}
		}
		echo "<br />";
	}
}

CloseTable();
//include("footer.php");
?>