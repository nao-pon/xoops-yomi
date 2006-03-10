<?php
// HYP Ticket Class based on GIJOE's Ticket Class by nao-pon
// GIJOE's Ticket Class (based on Marijuana's Oreteki XOOPS)
// nobunobu's suggestions are applied

if( ! class_exists( 'XoopsHypTicket' ) ) {

class XoopsHypTicket {

	var $_errors = array() ;
	var $_latest_token = '' ;
	var $check_ip = 0; //default IP check OFF
	var $timeout = 1800;

	// render form as plain html
	function getTicketHtml( $salt = '' , $timeout = 0 )
	{
		global $xoopsConfig;
		if (!$timeout) $timeout = $this->timeout;
		$sid = "";
		if (!($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '' && isset($_COOKIE[$xoopsConfig['session_name']])) && strpos(SID,session_name()."=") === 0)
		{
			$sid = explode("=",strip_tags(SID));
			$sid = '<input type="hidden" name="'.$sid[0].'" value="'.$sid[1].'" />' ;
		}
		return '<input type="hidden" name="XOOPS_HYP_TICKET" value="'.$this->issue( $salt , $timeout ).'" />'.$sid ;
	}

	// returns an object of XoopsFormHidden including theh ticket
	function getTicketXoopsForm( $salt = '' , $timeout = 0 )
	{
		if (!$timeout) $timeout = $this->timeout;
		return new XoopsFormHidden( 'XOOPS_HYP_TICKET' , $this->issue( $salt , $timeout ) ) ;
	}

	// returns an array for xoops_confirm() ;
	function getTicketArray( $salt = '' , $timeout = 0 )
	{
		if (!$timeout) $timeout = $this->timeout;
		return array( 'XOOPS_HYP_TICKET' => $this->issue( $salt , $timeout ) ) ;
	}

	// return GET parameter string.
	function getTicketParamString( $salt = '' , $noamp = false , $timeout = 0 )
	{
		global $xoopsConfig;
		if (!$timeout) $timeout = $this->timeout;
		$sid = (!($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '' && isset($_COOKIE[$xoopsConfig['session_name']])) && strpos(SID,session_name()."=") === 0)? "&amp;".strip_tags(SID) : "";
		return ( $noamp ? '' : '&amp;' ) . 'XOOPS_HYP_TICKET=' . $this->issue( $salt, $timeout ) . $sid;
	}

	// issue a ticket
	function issue( $salt = '' , $timeout = 0 )
	{
		if (!$timeout) $timeout = $this->timeout;
		// create a token
		list( $usec , $sec ) = explode( " " , microtime() ) ;
		$token = crypt( $salt . $usec . $_SERVER['PATH'] . $sec ) ;
		$this->_latest_token = $token ;

		// store stub
		$_SESSION['XOOPS_HYP_STUBS'][] = array(
			'expire' => time() + $timeout ,
			'ip' => $_SERVER['REMOTE_ADDR'] ,
			'token' => $token
		) ;

		// paid md5ed token as a ticket
		return md5( $token . XOOPS_DB_PREFIX ) ;
	}

	// check a ticket
	function check( $post = true )
	{

		$this->_errors = array() ;

		// CHECK: stubs are not stored in session
		if( empty( $_SESSION['XOOPS_HYP_STUBS'] ) || ! is_array($_SESSION['XOOPS_HYP_STUBS'])) {
			$this->clear() ;
			$this->_errors[] = 'Invalid Session' ;
			return false ;
		}

		// get key&val of the ticket from a user's query
		if( $post ) {
			$ticket = empty( $_POST['XOOPS_HYP_TICKET'] ) ? '' : $_POST['XOOPS_HYP_TICKET'] ;
		} else {
			$ticket = empty( $_GET['XOOPS_HYP_TICKET'] ) ? '' : $_GET['XOOPS_HYP_TICKET'] ;
		}

		// CHECK: no tickets found
		if( empty( $ticket ) ) {
			$this->clear() ;
			$this->_errors[] = 'Irregular post found' ;
			return false ;
		}

		// gargage collection & find a right stub
		$stubs_tmp = $_SESSION['XOOPS_HYP_STUBS'] ;
		$_SESSION['XOOPS_HYP_STUBS'] = array() ;
		foreach( $stubs_tmp as $stub ) {
			// default lifetime 30min
			if( $stub['expire'] >= time() ) {
				if( md5( $stub['token'] . XOOPS_DB_PREFIX ) === $ticket ) {
					$found_stub = $stub ;
				} else {
					// store the other valid stubs into session
					$_SESSION['XOOPS_HYP_STUBS'][] = $stub ;
				}
			} else {
				if( md5( $stub['token'] . XOOPS_DB_PREFIX ) === $ticket ) {
					// not CSRF but Time-Out
					$timeout_flag = true ;
				}
			}
		}

		// CHECK: no right stub found
		if( empty( $found_stub ) ) {
			$this->clear() ;
			if( empty( $timeout_flag ) ) $this->_errors[] = 'Invalid Session' ;
			else $this->_errors[] = 'Time out' ;
			return false ;
		}

		// CHECK: different ip
		if( $this->check_ip && $found_stub['ip'] != $_SERVER['REMOTE_ADDR'] ) {
			$this->clear() ;
			$this->_errors[] = 'IP has been changed' ;
			return false ;
		}

		// all green
		return true;
	}


	// clear all stubs
	function clear()
	{
		$_SESSION['XOOPS_HYP_STUBS'] = array() ;
	}


	// Ticket Using
	function using()
	{
		if( ! empty( $_SESSION['XOOPS_HYP_STUBS'] ) ) {
			return true;
		} else {
			return false;
		}
	}


	// return errors
	function getErrors( $ashtml = true )
	{
		if( $ashtml ) {
			$ret = '' ;
			foreach( $this->_errors as $msg ) {
				$ret .= "$msg<br />\n" ;
			}
		} else {
			$ret = $this->_errors ;
		}
		return $ret ;
	}

// end of class
}

// create a instance in global scope
$GLOBALS['xoopsHypTicket'] = new XoopsHypTicket() ;

}

if( ! function_exists( 'admin_refcheck' ) ) {

//Admin Referer Check By Marijuana(Rev.011)
function admin_refcheck($chkref = "") {
	if( empty( $_SERVER['HTTP_REFERER'] ) ) {
		return true ;
	} else {
		$ref = $_SERVER['HTTP_REFERER'];
	}
	$cr = XOOPS_URL;
	if ( $chkref != "" ) { $cr .= $chkref; }
	if ( strpos($ref, $cr) !== 0 ) { return false; }
	return true;
}

}

?>