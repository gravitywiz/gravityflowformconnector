<?php
/*
 * Purpose: Test the {workflow_form_submission_link} merge tag for the Form Submission type step in the form confirmation message.
 */

$I = new AcceptanceTester( $scenario );

$I->wantTo( 'Test the {workflow_form_submission_link} merge tag in the form confirmation message.' );

// Update the target page in the submission step
$form_id = GFFormsModel::get_form_id( '0004 Form Submission Merge Tag Source' );
$steps = gravity_flow()->get_steps( $form_id );
foreach ( $steps as $step ) {
	if ( $step->get_name() == 'Form Submission' ) {
		$submit_page = get_page_by_title( '0004 Form Submission Merge Tag Target' );
		$feed_meta = $step->get_feed_meta();
		$feed_meta['submit_page'] = $submit_page->ID;
		gravity_flow()->update_feed_meta( $step->get_id(), $feed_meta );
	}
}

// Make sure we're logged out
$I->logOut();
$I->resetCookie( 'gflow_access_token' );

// Submit the form
$I->amOnPage( '/0004-form-submission-merge-tag-source' );
$I->see( '0004 Form Submission Merge Tag Source' );
$I->seeInField( 'Source', 'The value' );
$I->seeInField( 'Email', 'test@test.test' );
$I->click( [ 'css' => 'input[type=submit]' ]);
// Verify the merge tag included a link in the confirmation message.
$I->waitForText( 'We will get in touch with you shortly.', 3 );
$I->dontSee( "Form Submission Merge Tag: {workflow_form_submission_link: assignee='email_field|2'}" );
$I->see( 'Form Submission Merge Tag:' );
$I->seeLink( '0004 Form Submission Merge Tag Target' );

// Click the link and verify the submit page loads with the target form populated.
$I->click( '0004 Form Submission Merge Tag Target' );
$I->waitForElement( [ 'css' => 'input[type=submit]' ] );

$I->seeInField( 'Target', 'The value' );
