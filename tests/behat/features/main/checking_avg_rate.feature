Feature: Average rate
  In order to check average rate
  As a programmer
  I need to be able to get the rate

  Scenario: Getting the rate of table "a"
    Given I have the NbpRepository object for table "a"
    When I input the date "2015-01-02"
    And I request the rates of USD, EUR and CHF
    Then I should get the rate 3.5725, 4.3078 and 3.5833

  Scenario: Getting the rate of work day before from table "a"
    Given I have the NbpRepository object for table "a"
    When I input the date "2013-01-15"
    And I request the rates of USD, EUR and CHF from work day before
    Then I should get the rates 3.0828, 4.1231 and 3.3674

  Scenario: Getting the rate of work day before when it was weekend
    Given I have the NbpRepository object for table "a"
    When I input the date "2013-01-14"
    And I request the rates of USD, EUR and CHF from work day before
    Then I should get the rates 3.0890, 4.0996 and 3.3693

  Scenario: Getting the rate of currency with multiplier from table "a"
    Given I have the NbpRepository object for table "a"
    When I input the date "2011-11-10"
    And I request the rate of JPY
    Then I should get the rate 414.23