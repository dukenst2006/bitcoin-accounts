<?php

namespace Jwz104\BitcoinAccounts\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

use Jwz104\BitcoinAccounts\Facades\BitcoinAccounts;

class BitcoinUser extends Model
{
    /**
     * The fillable columns
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The addresses of the user
     *
     * @return Jwz104\BitcoinAccounts\BitcoinAddress[]
     */
    public function addresses()
    {
        return $this->hasMany(BitcoinAddress::class);
    }
    
    /**
     * The transactions of the user
     *
     * @return Jwz104\Bitcoin\BitcoinTransaction[]
     */
    public function transactions()
    {
        return $this->hasMany(BitcoinTransaction::class)->orWhere('other_bitcoin_user_id', $this->id);
    }

    /**
     * Get the user balance from the users transactions
     * 
     * @return double
     */
    public function balance()
    {
        $balance = DB::select('SELECT GetBitcoinUserBalance(?) AS balance;', [$this->id])[0]->balance;
        return $balance;
    }

    /**
     * Create an address for the user and return it
     *
     * @return string
     */
    public function createAddress()
    {
        return BitcoinAccounts::createAddress($this);
    }

    /**
     * Send bitcoins to an address and return the txid
     * the amount of fee defined in the config file will be added to the amount
     *
     * @param $address Jwz104\BitcoinAccounts\Models\BitcoinAddress To address
     * @param $amount double The amount of bitcoins
     * @param $fee double The amount of fee, leave empty for default amount
     * @return string
     */
    public function sendToAddress($address, $amount, $fee = null)
    {
        return BitcoinAccounts::sendToAddress($this, $address, $amount, $fee);
    }

    /**
     * Send bitcoins to an user
     *
     * @param $touser Jwz104\BitcoinAccounts\Models\BitcoinUser The destination user
     * @param $amount double The amount of bitcoins
     * @return bool
     */
    public function sendToUser(BitcoinUser $touser, $amount)
    {
        return BitcoinAccounts::sendToUser($this, $touser, $amount);
    }

    /**
     * Change the user on the address to this user
     *
     * @param $address Jwz104\BitcoinAccounts\Models\BitcoinAddress The bitcoin address
     * @return bool
     */
    public function setAddressUser(BitcoinAddress $address)
    {
        return BitcoinAccounts::setAddressUser($this, $address);
    }

    /**
     * Transfer all bitcoin of the user to an address and return the txid
     *
     * @param $user Jwz104\BitcoinAccounts\Models\BitcoinUser The user
     * @param $address string The address
     * @param $fee double The fee, null for default fee
     * @throws Jwz104\BitcoinAccounts\Exceptions\LowBalanceException Thrown then the user balance minus the fee is 0 or lower
     * @return string
     */
    public function emptyAccountToAddress($address, $fee = null)
    {
        return BitcoinAccounts::emptyAccountToAddress($this, $address, $fee);
    }

    /**
     * Transfer all bitcoin of the user to an address and return the txid
     *
     * @param $touser Jwz104\BitcoinAccounts\Models\BitcoinUser The user
     * @throws Jwz104\BitcoinAccounts\Exceptions\LowBalanceException Thrown then the user balance is 0 or lower
     * @return void
     */
    public function emptyAccountToUser(BitcoinUser $touser)
    {
        return BitcoinAccounts::emptyAccountToUser($this, $touser);
    }
}
