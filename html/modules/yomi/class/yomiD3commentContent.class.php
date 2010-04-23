<?php

require_once XOOPS_TRUST_PATH.'/modules/d3forum/class/D3commentAbstract.class.php' ;

// a class for d3forum comment integration
class yomiD3commentContent extends D3commentAbstract {

function fetchSummary( $link_id )
{
	$db =& Database::getInstance() ;
	$myts =& MyTextsanitizer::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $this->mydirname ) ;

	$link_id = intval( $link_id ) ;
	$mydirname = $this->mydirname ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// query
	$data = $db->fetchArray( $db->query( "SELECT `title`, `message` FROM ".$db->prefix('yomi_log')." WHERE `id`=$link_id LIMIT 1" ) ) ;

	// get body
	$subject = $body = '';
	if (! empty($data['title'])) {
		// make subject
		$subject = $data['title'];
		$body = $data['message'];
	} else {
		$subject = _MD_D3FORUM_ERR_READPOST;
	}
	$uri = XOOPS_URL . '/modules/' . $mydirname . '/single_link.php?item_id=' . $link_id;

	return array(
		'dirname' => $mydirname ,
		'module_name' => $module->getVar( 'name' ) ,
		'subject' => $myts->makeTboxData4Show( $subject ) ,
		'uri' => $uri ,
		'summary' => xoops_substr( strip_tags( $body ) , 0 , 255 ) ,
	) ;
}

// get id from <{$content.id}>
function external_link_id( $params )
{
	if (is_object($this->smarty)) {
		$content = $this->smarty->get_template_vars( 'content' ) ;
		return intval( $content['item_id'] ) ;
	} else {
		return @$params['item_id'] ;
	}
}

// get escaped subject from <{$content.subject}>
function getSubjectRaw( $params )
{
	if (is_object($this->smarty)) {
		$content = $this->smarty->get_template_vars( 'content' ) ;
		return $this->unhtmlspecialchars( $content['subject'] , ENT_QUOTES ) ;
	} else {
		return empty( $params['subject_escaped'] ) ? @$params['subject'] : $this->unhtmlspecialchars( @$params['subject'] ) ;
	}
}


function onUpdate( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 )
{
	//exit('stop');
	$total_num = $this->getPostsCount( $forum_id , $link_id );
	include_once(XOOPS_ROOT_PATH.'/modules/yomi/include/comment_functions.php');
	yomi_com_update($link_id, $total_num);
	return true ;
}

function canPost( $link_id , $original_flag )
{
	static $check = array();
	if (isset($check[$link_id])) {
		return $check[$link_id];
	}
	if ($original_flag) {
		$db =& Database::getInstance() ;
		list($id) = $db->fetchRow( $db->query( "SELECT `id` FROM ".$db->prefix('yomi_log')." WHERE `id`=$link_id LIMIT 1" ) ) ;
		if (empty($id)) $original_flag = false;
	}
	$check[$link_id] = $original_flag;
	return $original_flag ;
}

function canReply( $link_id , $original_flag , $post_id )
{
	return $this->canPost( $link_id , $original_flag );
}

// class end
}