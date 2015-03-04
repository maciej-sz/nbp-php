Feature: Formatting Nbp date
  In order to format the date
  As a programmer
  I need to be able to get the formatted date

  Scenario: Formatting simple date
    Given I have date "2012-03-04"
    When I create the date object
    Then I should get the "120304" string in return

  Scenario: Formatting out of range date
    Given I have the date "2012-03-33"
    When I create the date object
    Then I should get the "120402" string in return