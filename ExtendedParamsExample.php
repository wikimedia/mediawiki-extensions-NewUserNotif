<?php
/**
 * Example Extension to provide additional parameters to the subject line and message body for NewUserNotif
 *
 * @file
 * @author Jack D. Pond <jack.pond@psitex.com>
 * @ingroup Extensions
 * @copyright 2011 Jack D. pond
 * @url https://www.mediawiki.org/wiki/Manual:Extensions
 * @license GPL-2.0-or-later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgExtensionFunctions[] = 'efNewUserNotifSetupExtension';

$wgExtensionCredits['other'][] = [
	'path' => __FILE__,
	'name' => 'AdditionalNewUserNotifParams',
	'author' => [ 'Jack D. Pond' ],
	'version' => '1.0',
	'url' => 'https://www.mediawiki.org/wiki/Extension:New_User_Email_Notification',
];

/**
 * Set up hooks for Additional NewUserNotif Parameters
 *
 * @return bool
 */
function efNewUserNotifSetupExtension() {
	global $wgHooks;

	$wgHooks['NewUserNotifSubject'][] = 'efNewUserNotifSubject';
	$wgHooks['NewUserNotifBody'][] = 'efNewUserNotifBody';

	return true;
}

/**
 * This function creates additional parameters which can be used in the email notification Subject Line for new users
 *
 * @param object NewUserNotifier $callobj
 * @param string $subjectLine Returns the message subject line
 * @param string $siteName Site Name of the Wiki
 * @param string $recipient Email/User Name of the Message Recipient.
 * @param User $user User name of the added user
 * @return bool
 */
function efNewUserNotifSubject( $callobj, $subjectLine, $siteName, $recipient, $user ) {
	$subjectLine = wfMessage(
		'newusernotifsubj',
		$siteName, // $1 Site Name
		$user->getName() // $2 User Name
	)->inContentLanguage()->text();

	return true;
}

/**
 * This function creates additional parameters which can be used in the email notification message body for new users
 *
 * @param object NewUserNotifier $callobj
 * @param string $messageBody Returns the message body.
 * @param string $siteName Site Name of the Wiki
 * @param string $recipient Email/User Name of the Message Recipient.
 * @param User $user User name of the added user
 * @return bool
 */
function efNewUserNotifBody( $callobj, $messageBody, $siteName, $recipient, $user ) {
	$contLang = MediaWiki\MediaWikiServices::getInstance()->getContentLanguage();
	$request = RequestContext::getMain()->getRequest();

	$messageBody = wfMessage(
		'newusernotifbody',
		$recipient, // $1 Recipient (of notification message)
		$user->getName(), // $2 User Name
		$siteName, // $3 Site Name
		$contLang->timeAndDate( wfTimestampNow() ), // $4 Time and date stamp
		$contLang->date( wfTimestampNow() ), // $5 Date Stamp
		$contLang->time( wfTimestampNow() ), // $6 Time Stamp
		$user->getEmail(), // $7 email
		rawurlencode( $siteName ), // $8 Site name encoded for email message link
		$request->getIP(), // $9 Submitter's IP Address
		rawurlencode( $user->getName() ) // $10 User Name encoded for email message link
	)->inContentLanguage()->text();

	return true;
}
