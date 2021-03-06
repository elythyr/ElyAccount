<?php

namespace ElyAccount\Tests\BankAccount;

use ElyAccount\BankAccount\AccountName;
use ElyAccount\BankAccount\AccountNumber;
use ElyAccount\BankAccount\CheckingAccount;
use ElyAccount\BankAccount\Exception\InvalidAmountOperation;
use ElyAccount\BankAccount\Exception\WrongCurrencyOperation;
use ElyAccount\Client\ClientId;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class CheckingAccountTest extends TestCase
{
    const EURO_CURRENCY = 'EUR';
    const BRITISH_POUND_CURRENCY = 'GBP';

    /**
     * @var CheckingAccount
     */
    private $sut;

    /**
     * @var ClientId
     */
    private $ownerId;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var AccountNumber
     */
    private $accountNumber;

    protected function setUp()
    {
        $this->ownerId = $this->createAnOwnerId();
        $this->accountNumber = $this->createAnAccountNumber();
        $this->currency = $this->createEuroCurrency();
        $this->sut = CheckingAccount::open(
            $this->accountNumber,
            $this->currency,
            $this->ownerId
        );
    }

    /**
     * @test
     */
    public function shouldOpenAnAccountWithNoFunds()
    {
        $this->assertEquals(0, $this->sut->balance()->getAmount());

        return $this->sut;
    }

    /**
     * @test
     */
    public function shouldNotAcceptADepositFromAnotherCurrency()
    {
        $this->expectException(WrongCurrencyOperation::class);

        $this->sut->deposit(
            $this->createAnAmount(399384, self::BRITISH_POUND_CURRENCY)
        );
    }

    /**
     * @test
     */
    public function shouldNotAcceptAWithdrawalFromAnotherCurrency()
    {
        $this->expectException(WrongCurrencyOperation::class);

        $this->sut->withdraw(
            $this->createAnAmount(399384, self::BRITISH_POUND_CURRENCY)
        );
    }

    /**
     * @test
     * @dataProvider provideInvalidAmounts
     */
    public function shouldNotAcceptADepositWithAnInvalidAmount(Money $amount)
    {
        $this->expectException(InvalidAmountOperation::class);

        $this->sut->deposit($amount);
    }

    /**
     * @test
     * @dataProvider provideInvalidAmounts
     */
    public function shouldNotAcceptANegativeWithdrawal(Money $amount)
    {
        $this->expectException(InvalidAmountOperation::class);

        $this->sut->withdraw($amount);
    }

    public function provideInvalidAmounts(): array
    {
        return [
            'negative amount' => [$this->createAnAmount(-46544)],
            'amount equals to zero' => [$this->createAnAmount(0)],
        ];
    }

    /**
     * @test
     * @depends shouldOpenAnAccountWithNoFunds
     */
    public function shouldMakeADeposit(CheckingAccount $sut)
    {
        $amount = $this->createAnAmount(443947);
        $sut->deposit($amount);

        $this->assertTrue($amount->equals($sut->balance()));

        return $sut;
    }

    /**
     * @test
     * @depends shouldMakeADeposit
     */
    public function shouldMakeAWithdrawal(CheckingAccount $sut)
    {
        $previousBalance = $sut->balance();
        $amount = $this->createAnAmount(130255);
        $sut->withdraw($amount);

        $this->assertTrue(
            $previousBalance->subtract($amount)->equals($sut->balance())
        );

        return $sut;
    }

    /**
     * @test
     * @depends shouldMakeAWithdrawal
     */
    public function shouldHaveANegativeBalance(CheckingAccount $sut)
    {
        $sut->withdraw($this->createAnAmount(349438));

        $this->assertEquals(-35746, $sut->balance()->getAmount());

        return $sut;
    }

    /**
     * @test
     * @depends shouldHaveANegativeBalance
     */
    public function shouldStillHaveANegativeBalance(CheckingAccount $sut)
    {
        $sut->deposit($this->createAnAmount(29468));

        $this->assertEquals(-6278, $sut->balance()->getAmount());

        return $sut;
    }

    /**
     * @test
     * @depends shouldStillHaveANegativeBalance
     */
    public function shouldBeBackToAPositiveAmount(CheckingAccount $sut)
    {
        $sut->deposit($this->createAnAmount(14578));

        $this->assertEquals(8300, $sut->balance()->getAmount());
    }

    /**
     * @test
     */
    public function shouldGetTheNumberOfAnAccount()
    {
        $this->assertSame($this->accountNumber, $this->sut->number());
    }

    /**
     * @test
     */
    public function shouldGetsTheStringRepresentingAnAccountWithoutAName()
    {
        $this->assertEquals(
            (string) $this->accountNumber,
            (string) $this->sut
        );
    }

    /**
     * @test
     */
    public function shouldGiveANameToAnAccount()
    {
        $name = AccountName::fromString('my daily account');
        $this->sut->rename($name);

        $this->assertEquals($name, $this->sut->name());
    }

    /**
     * @test
     */
    public function shouldGetsTheStringRepresentingANamedAccount()
    {
        $name = AccountName::fromString('never broke');
        $this->sut->rename($name);

        $this->assertEquals($name, (string) $this->sut);
    }

    /**
     * @test
     */
    public function shouldCheckIfAClientIsTheOwnerOfTheAccount()
    {
        $clientId = $this->createAnOwnerId();
        $clientId->expects($this->once())
            ->method('equals')
            ->with($this->identicalTo($this->ownerId));

        $this->sut->isOwner($clientId);
    }

    private function createAnAccountNumber(): AccountNumber
    {
        return AccountNumber::generate();
    }

    private function createAnAmount(int $value, string $currencyCode = null): Money
    {
        return new Money(
            $value,
            $this->createACurrency($currencyCode ?? self::EURO_CURRENCY)
        );
    }

    private function createACurrency(string $currencyCode): Currency
    {
        return new Currency($currencyCode);
    }
    private function createEuroCurrency(): Currency
    {
        return new Currency(self::EURO_CURRENCY);
    }

    private function createPoundCurrency(): Currency
    {
        return new Currency(self::BRITISH_POUND_CURRENCY);
    }

    private function createAnOwnerId(): ClientId
    {
        return $this->createMock(ClientId::class);
    }
}
