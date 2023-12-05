<?php

/**
 * @group headless
 */
class CRM_Aaa_AdhocTest extends CiviUnitTestCase {
  protected $assignee1;
  protected $assignee2;
  protected $target;
  protected $source;

  public function setUp():void {
    parent::setUp();
    $this->assignee1 = $this->individualCreate([
      'first_name' => 'test_assignee1',
      'last_name' => 'test_assignee1',
      'email' => 'test_assignee1@gmail.com',
    ]);
    $this->assignee2 = $this->individualCreate([
      'first_name' => 'test_assignee2',
      'last_name' => 'test_assignee2',
      'email' => 'testassignee2@gmail.com',
    ]);
    $this->target = $this->individualCreate();
    $this->source = $this->individualCreate();
  }

  /**
   * This is a bit messed up having a variable called name that means label but we don't want to fix it because it's a form member variable _activityTypeName that might be used in form hooks, so just make sure it doesn't flip between name and label. dev/core#1116
   */
  public function testActivityTypeNameIsReallyLabel(): void {
    $form = new CRM_Activity_Form_Activity();

    // the actual value is irrelevant we just need something for the tested function to act on
    $form->_currentlyViewedContactId = $this->source;

    // Let's make a new activity type that has a different name from its label just to be sure.
    $actParams = [
      'option_group_id' => 'activity_type',
      'name' => 'wp1234',
      'label' => 'Water Plants',
      'is_active' => 1,
      'is_default' => 0,
    ];
    $result = $this->callAPISuccess('option_value', 'create', $actParams);

    $form->_activityTypeId = $result['values'][$result['id']]['value'];
    $this->assertNotEmpty($form->_activityTypeId);

    // Do the thing we want to test
    $form->assignActivityType();

    $this->assertEquals('Water Plants', $form->_activityTypeName);

    // cleanup
    $this->callAPISuccess('option_value', 'delete', ['id' => $result['id']]);
  }
  
}
