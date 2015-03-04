<?php
namespace MaciejSzAt\NbpPhp;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use MaciejSz\NbpPhp\NbpDate;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $_inputDate = null;

    /**
     * @var null|NbpDate
     */
    private $_NbpDate = null;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }


    /**
     * @Given /^I have( the)? date \"(?<date>.*)\"$/
     */
    public function iHaveDate($date)
    {
        $this->_inputDate = $date;
    }

    /**
     * @When I create the date object
     */
    public function iCreateTheDateObject()
    {
        $this->_NbpDate = NbpDate::fromDateString($this->_inputDate);
    }

    /**
     * @Then I should get the :arg1 string in return
     */
    public function iShouldGetTheStringInReturn($arg1)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $arg1,
            $this->_NbpDate->toString()
        );
    }

}
